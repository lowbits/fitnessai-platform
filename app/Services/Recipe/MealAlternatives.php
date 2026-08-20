<?php

namespace App\Services\Recipe;

use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Builds card-ready recipe suggestions for the meal_alternatives widget. Shared
 * by the swap tool (alternatives for an existing meal) and the add tool
 * (suggestions for a brand new slot).
 */
class MealAlternatives
{
    private const TARGET = 5;

    public function __construct(
        private readonly RecipeFinder $finder,
        private readonly RecipeAffinity $affinity,
    ) {}

    /**
     * Swap payload for an existing meal: original plus alternatives with deltas.
     *
     * @return array<string, mixed> `cards` may be empty when nothing matches
     */
    public function for(User $user, Meal $meal, ?string $wish = null): array
    {
        $locale = $user->locale ?? 'en';

        $cards = $this->candidates($user, $meal->type, (int) $meal->calories, array_values(array_filter([$meal->recipe_id])), $wish)
            ->map(fn (Recipe $recipe) => MealAlternativeCard::make($recipe, $meal, $locale))
            ->values()
            ->all();

        return [
            'meal_id' => $meal->id,
            'slot' => $meal->type,
            'original' => [
                'name' => $meal->name,
                'calories' => (int) $meal->calories,
                'protein_g' => (int) $meal->protein_g,
                'thumbnail_url' => $meal->thumbnail_url,
            ],
            'cards' => $cards,
        ];
    }

    /**
     * Suggestion cards for a new slot the day doesn't have yet, sized to a target.
     *
     * @return array<int, array<string, mixed>>
     */
    public function suggest(User $user, string $mealType, int $targetKcal, ?string $wish = null): array
    {
        $locale = $user->locale ?? 'en';

        return $this->candidates($user, $mealType, $targetKcal, [], $wish)
            ->map(fn (Recipe $recipe) => [
                'recipe_id' => $recipe->id,
                'name' => $recipe->localizedName($locale),
                'thumbnail_url' => $recipe->thumbnail_url ?? Meal::placeholderThumbnailUrl($mealType),
                'kcal' => (int) $recipe->calories,
                'protein_g' => (int) $recipe->protein_g,
                'carbs_g' => (int) $recipe->carbs_g,
                'fat_g' => (int) $recipe->fat_g,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $excludeIds
     * @return Collection<int, Recipe>
     */
    private function candidates(User $user, string $mealType, int $targetKcal, array $excludeIds, ?string $wish): Collection
    {
        $profile = $user->profile;
        $diet = $profile?->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $allowedProteins = array_map(fn (PrimaryProtein $p) => $p->value, PrimaryProtein::allowedFor($diet));
        $dislikes = array_map(fn (string $d) => mb_strtolower(trim($d)), $profile?->food_dislikes ?? []);

        return $this->finder->findCandidates(
            mealType: $mealType,
            targetKcal: $targetKcal,
            locale: $user->locale ?? 'en',
            allowedProteins: $allowedProteins,
            dislikes: $dislikes,
            forbiddenAxes: collect(),
            excludeIds: $excludeIds,
            affinityScores: $this->affinity->scoresFor($user)->all(),
            limit: self::TARGET,
            query: $wish,
            constrainToMeal: $wish === null,
        );
    }
}
