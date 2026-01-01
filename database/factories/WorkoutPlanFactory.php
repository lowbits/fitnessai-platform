<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutPlan>
 */
class WorkoutPlanFactory extends Factory
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
            'date' => fake()->dateTimeBetween('now', '+30 days'),
            'day_number' => fake()->numberBetween(1, 28),
            'status' => fake()->randomElement(['pending', 'generated', 'failed']),
            'workout_name' => fake()->randomElement(['Upper Body Strength', 'Lower Body Power', 'Full Body Workout', 'Cardio Session', 'Core Training']),
            'workout_type' => fake()->randomElement(['strength', 'cardio', 'hiit', 'rest', 'mobility']),
            'estimated_duration_minutes' => fake()->numberBetween(30, 90),
            'estimated_calories_burned' => fake()->numberBetween(200, 600),
            'difficulty' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'description' => fake()->sentence(10),
            'muscle_groups' => fake()->randomElements(['chest', 'back', 'legs', 'shoulders', 'arms', 'core'], fake()->numberBetween(2, 4)),
        ];
    }
}

