<?php

namespace Database\Factories;

use App\Models\WorkoutPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
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
            'order' => fake()->numberBetween(1, 10),
            'name' => fake()->randomElement(['Bench Press', 'Squat', 'Deadlift', 'Pull-ups', 'Push-ups', 'Lunges', 'Plank']),
            'type' => fake()->randomElement(['strength', 'cardio', 'warmup', 'cooldown', 'stretch']),
            'description' => fake()->sentence(10),
            'video_url' => fake()->url(),
            'image' => fake()->imageUrl(),
            'sets' => fake()->numberBetween(3, 5),
            'reps' => fake()->numberBetween(8, 15),
            'duration_seconds' => fake()->numberBetween(30, 300),
            'rest_seconds' => (string) fake()->numberBetween(30, 90),
            'tempo' => '2-1-2-0',
            'weight_recommendation' => fake()->randomElement(['bodyweight', 'light', 'moderate', 'heavy']),
            'muscle_groups' => fake()->randomElements(['chest', 'back', 'legs', 'shoulders', 'arms'], fake()->numberBetween(1, 3)),
            'equipment' => fake()->randomElements(['barbell', 'dumbbell', 'bodyweight', 'resistance band'], fake()->numberBetween(1, 2)),
            'form_cues' => fake()->sentence(),
            'alternatives' => fake()->randomElements(['Exercise A', 'Exercise B', 'Exercise C'], fake()->numberBetween(1, 2)),
            'difficulty' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
        ];
    }
}

