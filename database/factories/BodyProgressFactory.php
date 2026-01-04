<?php

namespace Database\Factories;

use App\Models\BodyProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BodyProgress>
 */
class BodyProgressFactory extends Factory
{
    protected $model = BodyProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'weight_kg' => fake()->randomFloat(2, 50, 150),
            'body_fat_percentage' => fake()->optional()->randomFloat(2, 5, 40),
            'muscle_mass_kg' => fake()->optional()->randomFloat(2, 20, 80),
            'waist_circumference_cm' => fake()->optional()->randomFloat(2, 60, 120),
            'chest_circumference_cm' => fake()->optional()->randomFloat(2, 70, 130),
            'hip_circumference_cm' => fake()->optional()->randomFloat(2, 70, 130),
            'arm_circumference_cm' => fake()->optional()->randomFloat(2, 20, 50),
            'thigh_circumference_cm' => fake()->optional()->randomFloat(2, 40, 80),
            'notes' => fake()->optional()->sentence(),
            'recorded_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}

