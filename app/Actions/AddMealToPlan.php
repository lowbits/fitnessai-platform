<?php

namespace App\Actions;

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;

/**
 * Adds a recipe as a new meal on the user's plan for today. Returns null when
 * there is no plan for today to add to.
 */
class AddMealToPlan
{
    public function execute(User $user, Recipe $recipe, string $type): ?Meal
    {
        $plan = $user->plans()->where('status', 'active')->first();
        if (! $plan) {
            return null;
        }

        $mealPlan = MealPlan::where('plan_id', $plan->id)->whereDate('date', today())->first();
        if (! $mealPlan) {
            return null;
        }

        return $mealPlan->meals()->create([
            'recipe_id' => $recipe->id,
            'type' => $type,
            'name' => $recipe->name,
            'description' => $recipe->description,
            'calories' => $recipe->calories,
            'protein_g' => $recipe->protein_g,
            'carbs_g' => $recipe->carbs_g,
            'fat_g' => $recipe->fat_g,
            'fiber_g' => $recipe->fiber_g,
            'sugar_g' => $recipe->sugar_g,
            'ingredients' => $recipe->ingredients,
            'instructions' => $recipe->instructions,
            'prep_time_minutes' => $recipe->prep_time_minutes,
            'cook_time_minutes' => $recipe->cook_time_minutes,
            'difficulty' => $recipe->difficulty,
            'servings' => 1,
            'status' => 'generated',
        ]);
    }
}
