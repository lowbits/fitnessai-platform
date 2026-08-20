<?php

namespace App\Services\Recipe;

use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Builds the meal_alternatives widget payload for a meal: the original plus
 * card-ready replacement recipes with macro deltas. Shared by the swap tool and
 * the add-meal tool so both render the exact same widget.
 */
class MealAlternatives
{
    private const TARGET = 5;

    public function __construct(
        private readonly RecipeFinder $finder,
        private readonly RecipeAffinity $affinity,
    ) {}

    /**
     * @return array<string, mixed> widget data; `cards` may be empty when nothing matches
     */
    public function for(User $user, Meal $meal, ?string $wish = null): array
    {
        $locale = $user->locale ?? 'en';

        $cards = $this->candidates($user, $meal, $wish)
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
     * @return Collection<int, Recipe>
     */
    private function candidates(User $user, Meal $meal, ?string $wish)
    {
        $profile = $user->profile;
        $diet = $profile?->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $allowedProteins = array_map(fn (PrimaryProtein $p) => $p->value, PrimaryProtein::allowedFor($diet));
        $dislikes = array_map(fn (string $d) => mb_strtolower(trim($d)), $profile?->food_dislikes ?? []);

        return $this->finder->findCandidates(
            mealType: $meal->type,
            targetKcal: (int) $meal->calories,
            locale: $user->locale ?? 'en',
            allowedProteins: $allowedProteins,
            dislikes: $dislikes,
            forbiddenAxes: collect(),
            excludeIds: array_values(array_filter([$meal->recipe_id])),
            affinityScores: $this->affinity->scoresFor($user)->all(),
            limit: self::TARGET,
            query: $wish,
            constrainToMeal: $wish === null,
        );
    }
}
