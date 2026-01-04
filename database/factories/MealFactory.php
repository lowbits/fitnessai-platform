<?php

namespace Database\Factories;

use App\Models\MealPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meal_plan_id' => MealPlan::factory(),
            'type' => fake()->randomElement(['breakfast', 'lunch', 'dinner', 'snack']),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'image' => null,
            'calories' => fake()->numberBetween(200, 800),
            'protein_g' => fake()->randomFloat(2, 10, 50),
            'carbs_g' => fake()->randomFloat(2, 20, 100),
            'fat_g' => fake()->randomFloat(2, 5, 40),
            'fiber_g' => fake()->randomFloat(2, 2, 15),
            'sugar_g' => fake()->randomFloat(2, 5, 30),
            'ingredients' => fake()->words(5),
            'instructions' => [fake()->sentence(), fake()->sentence()],
            'prep_time_minutes' => fake()->numberBetween(5, 30),
            'cook_time_minutes' => fake()->numberBetween(0, 60),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'servings' => fake()->numberBetween(1, 4),
            'tags' => fake()->words(3),
            'allergens' => fake()->randomElement([['gluten', 'dairy'], ['nuts'], []]),
        ];
    }
}

