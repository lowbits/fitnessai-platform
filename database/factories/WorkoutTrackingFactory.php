<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutTracking>
 */
class WorkoutTrackingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'user_id' => User::factory(),
            'workout_plan_id' => WorkoutPlan::factory(),
            'started_at' => $startedAt,
            'completed_at' => fake()->optional(0.7)->dateTimeBetween($startedAt, 'now'),
            'notes' => fake()->optional(0.5)->sentence(),
            'feeling_rate' => fake()->optional(0.6)->numberBetween(1, 5),
        ];
    }

    /**
     * Indicate that the workout is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => fake()->dateTimeBetween($attributes['started_at'], 'now'),
        ]);
    }

    /**
     * Indicate that the workout is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => null,
        ]);
    }
}

