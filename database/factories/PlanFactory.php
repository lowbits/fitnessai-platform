<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-30 days', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+60 days');

        return [
            'user_id' => User::factory(),
            'plan_name' => fake()->randomElement(['Muscle Gain Plan', 'Weight Loss Plan', 'Maintenance Plan', 'Strength Plan']),
            'status' => fake()->randomElement(['active', 'paused', 'completed', 'cancelled']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => fake()->numberBetween(14, 90),
            'daily_calories' => fake()->numberBetween(1500, 3500),
            'daily_protein_g' => fake()->numberBetween(100, 200),
            'daily_carbs_g' => fake()->numberBetween(150, 350),
            'daily_fat_g' => fake()->numberBetween(40, 100),
            'workouts_per_week' => fake()->numberBetween(3, 6),
        ];
    }

    /**
     * Indicate that the plan is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the plan is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}

