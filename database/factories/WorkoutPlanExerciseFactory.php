<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutPlanExercise>
 */
class WorkoutPlanExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_plan_id' => WorkoutPlan::factory(),
            'exercise_id' => Exercise::factory(),
            'order' => fake()->numberBetween(1, 10),
            'type' => fake()->randomElement(['strength', 'cardio', 'warmup', 'cooldown', 'stretch']),
            'sets' => fake()->numberBetween(3, 5),
            'reps' => fake()->numberBetween(8, 15),
            'duration_seconds' => fake()->numberBetween(30, 300),
            'rest_seconds' => (string) fake()->numberBetween(30, 90),
            'tempo' => '2-1-2-0',
            'weight_recommendation' => fake()->randomElement(['bodyweight', 'light', 'moderate', 'heavy']),
            'alternatives' => fake()->randomElements(['Exercise A', 'Exercise B', 'Exercise C'], fake()->numberBetween(1, 2)),
            'difficulty' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
        ];
    }
}
