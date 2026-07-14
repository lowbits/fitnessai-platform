<?php

namespace App\Ai\Tools;

use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use App\Services\Recipe\MealAlternativeCard;
use App\Services\Recipe\RecipeAffinity;
use App\Services\Recipe\RecipeFinder;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Proposes replacement meals for one of the user's meals. The AI passes the
 * meal_id (from get_today_meals or the seeded context); ownership is verified
 * here since the id comes from the model. Reuses RecipeFinder + RecipeAffinity
 * and returns card-ready data with macro deltas vs the original meal.
 */
class ProposeMealAlternativesTool implements Tool
{
    private const TARGET = 5;

    public function __construct(
        private readonly User $user,
        private readonly RecipeFinder $finder,
        private readonly RecipeAffinity $affinity,
    ) {}

    public function description(): Stringable|string
    {
        return 'Proposes replacement meals for one of the user\'s meals from existing recipes. Requires meal_id (get it from get_today_meals if you don\'t already have it). Pass what the user asked for as wish — a dish ("chili con carne"), a preference ("more protein", "under 20 minutes", "lighter"), or ingredients they have ("chicken, rice, broccoli"). The app renders the returned cards — do not list them in prose.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'meal_id' => $schema->integer()
                ->description('ID of the meal to replace. Must be one of the user\'s own meals (from get_today_meals or the current context).')
                ->required(),
            'wish' => $schema->string()
                ->description('What the user asked for in the replacement — a specific dish ("chili con carne") or a preference ("more protein", "lighter", "under 20 minutes"). Omit to just show the best-fitting options.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $mealId = (int) ($request['meal_id'] ?? 0);
        $wish = filled($request['wish'] ?? null) ? trim((string) $request['wish']) : null;

        $meal = Meal::with(['mealPlan.plan', 'recipe'])->find($mealId);

        if (! $meal || $meal->mealPlan?->plan?->user_id !== $this->user->id) {
            Log::warning('[Coach][ProposeAlternatives] Meal not found or not owned', [
                'meal_id' => $mealId,
                'user_id' => $this->user->id,
            ]);

            return json_encode(['error' => 'meal_not_found', 'cards' => []]);
        }

        if ($meal->isCompleted()) {
            Log::info('[Coach][ProposeAlternatives] Meal already eaten — not replaceable', [
                'meal_id' => $mealId,
                'user_id' => $this->user->id,
            ]);

            return json_encode([
                'error' => 'meal_already_eaten',
                'message' => 'This meal has already been eaten and can no longer be replaced.',
                'cards' => [],
            ]);
        }

        $locale = $this->user->locale ?? 'en';
        $cards = $this->findCandidates($meal, $wish)
            ->map(fn (Recipe $recipe) => MealAlternativeCard::make($recipe, $meal, $locale))
            ->values()
            ->all();

        Log::debug('[Coach][ProposeAlternatives] Built cards', [
            'meal_id' => $meal->id,
            'meal_type' => $meal->type,
            'wish' => $wish,
            'card_count' => count($cards),
        ]);

        return json_encode([
            'widget' => 'meal_alternatives',
            'requires_input' => true,
            'data' => [
                'meal_id' => $meal->id,
                'slot' => $meal->type,
                'original' => [
                    'name' => $meal->name,
                    'calories' => (int) $meal->calories,
                    'protein_g' => (int) $meal->protein_g,
                    'image_url' => $meal->recipe?->thumbnail_url,
                ],
                'cards' => $cards,
            ],
        ]);
    }

    /**
     * @return Collection<int, Recipe>
     */
    private function findCandidates(Meal $meal, ?string $wish): Collection
    {
        $profile = $this->user->profile;
        $diet = $profile?->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $allowedProteins = array_map(fn (PrimaryProtein $p) => $p->value, PrimaryProtein::allowedFor($diet));
        $dislikes = array_map(fn (string $d) => mb_strtolower(trim($d)), $profile?->food_dislikes ?? []);

        return $this->finder->findCandidates(
            mealType: $meal->type,
            targetKcal: (int) $meal->calories,
            locale: $this->user->locale ?? 'en',
            allowedProteins: $allowedProteins,
            dislikes: $dislikes,
            forbiddenAxes: collect(),
            excludeIds: array_values(array_filter([$meal->recipe_id])),
            affinityScores: $this->affinity->scoresFor($this->user)->all(),
            limit: self::TARGET,
            query: $wish,
            constrainToMeal: $wish === null,
        );
    }
}
