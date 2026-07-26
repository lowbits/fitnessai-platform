<?php

namespace App\Services\Recipe;

use App\Models\Meal;
use App\Models\Recipe;

/**
 * Builds the card-ready payload for a replacement recipe shown against the
 * meal it would replace. Shared by ProposeMealAlternativesTool (search) and
 * CreateRecipeTool (generate) so both render the exact same meal_alternatives
 * card — macro deltas included.
 */
final class MealAlternativeCard
{
    /**
     * @return array<string, mixed>
     */
    public static function make(Recipe $recipe, Meal $meal, string $locale): array
    {
        $kcal = (int) $recipe->calories;
        $protein = (int) $recipe->protein_g;

        return [
            'recipe_id' => $recipe->id,
            'name' => $recipe->localizedName($locale),
            'thumbnail_url' => $recipe->thumbnail_url ?? Meal::placeholderThumbnailUrl($meal->type),
            'kcal' => $kcal,
            'protein_g' => $protein,
            'carbs_g' => (int) $recipe->carbs_g,
            'fat_g' => (int) $recipe->fat_g,
            'delta_kcal' => $kcal - (int) $meal->calories,
            'delta_protein' => $protein - (int) $meal->protein_g,
        ];
    }
}
