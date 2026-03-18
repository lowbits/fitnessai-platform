<?php

namespace Database\Factories;

use App\Models\WorkoutPlanExercise;
use App\Models\WorkoutTracking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutTrackingExercise>
 */
class WorkoutTrackingExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_tracking_id' => WorkoutTracking::factory(),
            'workout_plan_exercise_id' => WorkoutPlanExercise::factory(),
            'order' => fake()->numberBetween(1, 10),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
