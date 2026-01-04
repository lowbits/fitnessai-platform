<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalorieTracking>
 */
class CalorieTrackingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $calories = fake()->numberBetween(100, 1500);

        return [
            'user_id' => User::factory(),
            'meal_id' => null,
            'tracked_date' => fake()->date(),
            'calories' => $calories,
            'protein_g' => fake()->randomFloat(2, 5, 80),
            'carbs_g' => fake()->randomFloat(2, 10, 150),
            'fat_g' => fake()->randomFloat(2, 5, 50),
            'meal_name' => fake()->randomElement([
                'Breakfast',
                'Lunch',
                'Dinner',
                'Snack',
                'Protein Shake',
                null,
            ]),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}

