<?php

namespace Database\Factories;

use App\Models\WorkoutTrackingExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutTrackingSet>
 */
class WorkoutTrackingSetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_tracking_exercise_id' => WorkoutTrackingExercise::factory(),
            'set_number' => fake()->numberBetween(1, 5),
            'reps' => fake()->numberBetween(5, 15),
            'weight' => fake()->randomFloat(1, 5, 150),
            'duration' => fake()->optional(0.3)->numberBetween(10, 120),
            'rpe' => fake()->optional(0.4)->numberBetween(1, 10),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
