<?php

namespace App\Http\Controllers\Api\V3;

use App\Ai\Prompts\MealAlternativeTitlesPrompt;
use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;
use App\Helpers\ToolCallHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V3\MealContextResource;
use App\Http\Resources\Api\V3\RecipeSuggestionResource;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserProfile;
use App\OpenAITools\MealToolDefinition;
use App\Services\Recipe\RecipeAffinity;
use App\Services\Recipe\RecipeFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

/**
 * V3 meal alternatives — clean shape, split concerns.
 *
 * Response separates two fundamentally different resources:
 *   - recipe_suggestions: real recipes from our DB (rich, linkable, image-ready)
 *   - ai_suggestions:     ephemeral title hints to feed into the replace flow
 *
 * Strategy: DB-first. Pull up to TARGET affinity-sorted recipes; fill the gap
 * with AI title hints only if needed. Skipping the AI call when the DB has
 * enough hits is both cheaper and gives the user a better experience —
 * they see real recipes with real images instead of generated names.
 */
class MealAlternativesController extends Controller
{
    private const TARGET = 5;

    public function __invoke(Request $request, Meal $meal, RecipeFinder $finder, RecipeAffinity $affinity): JsonResponse
    {
        Gate::authorize('update', $meal);

        $user = $request->user();
        $profile = $user->profile;

        $validated = Validator::validate($request->all(), [
            'hint' => 'nullable|string|max:500',
            'exclude_recipe_ids' => 'sometimes|array|max:200',
            'exclude_recipe_ids.*' => 'integer',
        ]);

        try {
            $hint = $validated['hint'] ?? null;
            $recipes = $this->findRecipeSuggestions($meal, $user, $profile, $finder, $affinity, $validated['exclude_recipe_ids'] ?? [], $hint);
            $aiTitles = $this->fillWithAiTitles($meal, $user, $profile, $hint, $recipes);

            Log::info('[MealAlternatives] Served', [
                'meal_id' => $meal->id,
                'user_id' => $user->id,
                'hint' => $hint,
                'db_count' => $recipes->count(),
                'ai_count' => $aiTitles->count(),
                'excluded' => count($validated['exclude_recipe_ids'] ?? []),
            ]);

            return response()->json([
                'recipe_suggestions' => RecipeSuggestionResource::collection($recipes),
                'ai_suggestions' => $aiTitles->map(fn (string $title) => ['title' => $title])->values(),
                'original_meal' => new MealContextResource($meal),
            ]);
        } catch (Throwable $e) {
            Log::error('[MealAlternatives] Failed', [
                'meal_id' => $meal->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'An error occurred while generating meal alternatives.',
            ], 500);
        }
    }

    /**
     * @param  list<int>  $excludeRecipeIds  Client-supplied — already seen this session.
     * @return Collection<int, Recipe>
     */
    private function findRecipeSuggestions(Meal $meal, User $user, UserProfile $profile, RecipeFinder $finder, RecipeAffinity $affinity, array $excludeRecipeIds = [], ?string $hint = null): Collection
    {
        $diet = $profile->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $allowedProteins = array_map(fn (PrimaryProtein $p) => $p->value, PrimaryProtein::allowedFor($diet));
        $dislikes = array_map(fn (string $d) => mb_strtolower(trim($d)), $profile->food_dislikes ?? []);

        $exclude = array_values(array_unique(array_merge(
            array_filter([$meal->recipe_id]),
            $excludeRecipeIds,
        )));

        return $finder->findCandidates(
            mealType: $meal->type,
            targetKcal: (int) $meal->calories,
            locale: $user->locale ?? 'en',
            allowedProteins: $allowedProteins,
            dislikes: $dislikes,
            forbiddenAxes: collect(),
            excludeIds: $exclude,
            affinityScores: $affinity->scoresFor($user)->all(),
            limit: self::TARGET,
            query: $hint,
        );
    }

    /**
     * @param  Collection<int, Recipe>  $existingRecipes
     * @return Collection<int, string>
     */
    private function fillWithAiTitles(Meal $meal, User $user, UserProfile $profile, ?string $hint, Collection $existingRecipes): Collection
    {
        $remaining = self::TARGET - $existingRecipes->count();
        if ($remaining <= 0) {
            return collect();
        }

        $avoidNames = $existingRecipes->pluck('name')->all();

        $titles = $this->generateAiTitles($meal, $user, $profile, $hint, $remaining, $avoidNames);

        return collect($titles)->take($remaining)->values();
    }

    /**
     * @param  list<string>  $avoidNames
     * @return list<string>
     */
    private function generateAiTitles(Meal $meal, User $user, UserProfile $profile, ?string $hint, int $count, array $avoidNames): array
    {
        $systemPrompt = (string) new MealAlternativeTitlesPrompt($meal, $user, $profile, $count, $avoidNames);

        $userMessage = "Current {$meal->type}: {$meal->name} ({$meal->calories} kcal)\n";
        $userMessage .= "Macros: Protein {$meal->protein_g}g, Carbs {$meal->carbs_g}g, Fat {$meal->fat_g}g\n\n";
        if ($hint) {
            $userMessage .= "User preference: {$hint}\n\n";
        }
        $userMessage .= "Generate {$count} meal TITLES.\n";

        $response = OpenAI::responses()->create([
            'model' => config('ai.models.simple'),
            'input' => $userMessage,
            'instructions' => $systemPrompt,
            'tools' => [MealToolDefinition::getProvideMealTitlesTool()],
            'tool_choice' => 'required',
        ]);

        $arguments = ToolCallHelper::extractToolCall(
            $response,
            'provide_meal_titles',
            fn ($args) => isset($args['titles']) && is_array($args['titles']),
        );

        return array_slice($arguments['titles'], 0, $count);
    }
}
