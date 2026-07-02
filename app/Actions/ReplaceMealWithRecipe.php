<?php

namespace App\Actions;

use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;

class ReplaceMealWithRecipe
{
    public function execute(Meal $meal, Recipe $recipe): Meal
    {
        return DB::transaction(function () use ($meal, $recipe): Meal {
            $newMeal = Meal::create([
                'meal_plan_id' => $meal->meal_plan_id,
                'recipe_id' => $recipe->id,
                'type' => $meal->type,
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
                'tags' => $recipe->tags,
                'allergens' => $recipe->allergens,
                'primary_protein' => $recipe->primary_protein,
                'cuisine' => $recipe->cuisine,
                'format' => $recipe->format,
                'hero_veg' => $recipe->hero_veg,
                'status' => 'generated',
            ]);

            $meal->delete();

            return $newMeal;
        });
    }
}
