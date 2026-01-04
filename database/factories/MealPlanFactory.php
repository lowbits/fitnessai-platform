<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealPlan>
 */
class MealPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'date' => fake()->date(),
            'day_number' => fake()->numberBetween(1, 90),
            'status' => 'generated',
            'total_calories' => fake()->numberBetween(1500, 3000),
            'total_protein_g' => fake()->randomFloat(2, 80, 200),
            'total_carbs_g' => fake()->randomFloat(2, 150, 400),
            'total_fat_g' => fake()->randomFloat(2, 40, 120),
        ];
    }
}

