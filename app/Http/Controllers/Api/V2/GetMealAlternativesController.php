<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;
use App\Helpers\ToolCallHelper;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Recipe;
use App\OpenAITools\MealToolDefinition;
use App\Services\Recipe\RecipeFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Generate 5 meal-swap suggestions for an existing meal.
 *
 * Strategy: DB-first, AI-fallback.
 *
 *   1. Pull up to 5 affinity-sorted recipes from our DB that match the slot's
 *      dietary + macro constraints. Favorites surface naturally; recently-
 *      served-non-favorites are cooled down via RecipeAffinity.
 *   2. If fewer than 5 hits, ask AI for the remaining titles. The AI sees
 *      the DB names so it doesn't duplicate them.
 *
 * Returns the same `titles` array shape mobile expects — wire-compatible.
 */
class GetMealAlternativesController extends Controller
{
    private const ALTERNATIVES_TARGET = 5;

    public function __invoke(Request $request, Meal $meal, RecipeFinder $finder): JsonResponse
    {
        Gate::authorize('update', $meal);

        $user = $request->user();
        $profile = $user->profile;

        $validated = Validator::validate($request->all(), [
            'hint' => 'nullable|string|max:500',
        ]);

        try {
            $dbRecipes = $this->fetchFromDatabase($meal, $user, $profile, $finder);

            $dbTitles = $dbRecipes->pluck('name')->all();
            $remaining = self::ALTERNATIVES_TARGET - count($dbTitles);

            $aiTitles = $remaining > 0
                ? $this->generateAiTitles($meal, $profile, $user, $validated['hint'] ?? null, $remaining, $dbTitles)
                : [];

            return response()->json([
                'titles' => array_merge($dbTitles, $aiTitles),
                'source' => [
                    'db' => count($dbTitles),
                    'ai' => count($aiTitles),
                ],
                'original_meal' => [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'type' => $meal->type,
                    'calories' => $meal->calories,
                    'protein_g' => $meal->protein_g,
                    'carbs_g' => $meal->carbs_g,
                    'fat_g' => $meal->fat_g,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to generate meal alternatives', [
                'meal_id' => $meal->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to generate alternatives',
                'message' => 'An error occurred while generating meal alternatives. Please try again.',
            ], 500);
        }
    }

    /**
     * @return Collection<int, Recipe>
     */
    private function fetchFromDatabase(Meal $meal, $user, $profile, RecipeFinder $finder): Collection
    {
        $diet = $profile->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $allowedProteins = array_map(fn (PrimaryProtein $p) => $p->value, PrimaryProtein::allowedFor($diet));
        $dislikes = array_map(fn (string $d) => mb_strtolower(trim($d)), $profile->food_dislikes ?? []);

        return $finder->findCandidates(
            mealType: $meal->type,
            targetKcal: (int) $meal->calories,
            locale: $user->locale ?? 'en',
            allowedProteins: $allowedProteins,
            dislikes: $dislikes,
            forbiddenAxes: collect(),
            excludeIds: array_filter([$meal->recipe_id]),
            limit: self::ALTERNATIVES_TARGET,
        );
    }

    /**
     * @param  list<string>  $existingTitles
     * @return list<string>
     */
    private function generateAiTitles(Meal $meal, $profile, $user, ?string $hint, int $count, array $existingTitles): array
    {
        $language = match ($user->locale) {
            'de' => 'German language',
            default => 'English language',
        };

        $avoidLine = empty($existingTitles)
            ? ''
            : "\n\nDO NOT suggest any of these titles (they are already shown to the user): ".implode(', ', $existingTitles);

        $systemPrompt = <<<PROMPT
You are a world-class nutritionist specializing in personalized meal alternatives.

**User Profile:**
- Body Goal: {$profile->body_goal->value}
- Diet Type: {$profile->getDietaryInfo()}

**Original Meal Context:**
- Meal Type: {$meal->type}
- Current Meal: {$meal->name}
- Target Calories: {$meal->calories} kcal
- Target Protein: {$meal->protein_g}g
- Target Carbs: {$meal->carbs_g}g
- Target Fat: {$meal->fat_g}g

**Your Mission:**
Generate {$count} appealing meal TITLES ONLY (not full recipes) as alternatives for this {$meal->type}.

**Critical Requirements:**
1. Each title should suggest a meal with similar macros (±15%)
2. STRICTLY follow {$profile->getDietaryInfo()} diet principles
3. Match the meal type ({$meal->type})
4. Create exciting, varied options (different proteins, cooking methods, cuisines)
5. Use {$language} for ALL titles
6. Make titles appetizing and descriptive (e.g., "Grilled Salmon with Lemon Herb Quinoa"){$avoidLine}

Generate exactly {$count} creative meal titles that match the nutritional profile!
PROMPT;

        $userMessage = "Current {$meal->type}: {$meal->name} ({$meal->calories} kcal)\n";
        $userMessage .= "Macros: Protein {$meal->protein_g}g, Carbs {$meal->carbs_g}g, Fat {$meal->fat_g}g\n\n";

        if ($hint) {
            $userMessage .= "User preference: {$hint}\n\n";
        }

        $userMessage .= "Generate {$count} meal TITLES as alternatives.\n";

        Log::info('Generating meal title alternatives (AI fallback)', [
            'meal_id' => $meal->id,
            'requested_count' => $count,
            'with_hint' => ! empty($hint),
            'existing_db_titles' => count($existingTitles),
        ]);

        $response = OpenAI::responses()->create([
            'model' => config('ai.models.simple'),
            'input' => $userMessage,
            'instructions' => $systemPrompt,
            'tools' => [
                MealToolDefinition::getProvideMealTitlesTool(),
            ],
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
