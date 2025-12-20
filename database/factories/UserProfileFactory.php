<?php

namespace Database\Factories;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;
use App\Enums\Gender;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'age' => fake()->numberBetween(18, 65),
            'gender' => fake()->randomElement(Gender::cases()),
            'weight' => fake()->randomFloat(1, 50, 120),
            'height' => fake()->randomFloat(1, 150, 200),
            'body_goal' => fake()->randomElement(BodyGoal::cases()),
            'skill_level' => fake()->randomElement(SkillLevel::cases()),
            'activity_level' => fake()->randomElement(ActivityLevel::cases()),
            'training_place' => fake()->randomElement(TrainingPlace::cases()),
            'diet_type' => fake()->randomElement(DietType::cases()),
            'training_sessions_per_week' => fake()->numberBetween(1, 7),
        ];
    }

    /**
     * Indicate that the profile is for a male user.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => Gender::MALE,
        ]);
    }

    /**
     * Indicate that the profile is for a female user.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => Gender::FEMALE,
        ]);
    }

    /**
     * Indicate that the profile has a muscle gain goal.
     */
    public function muscleGain(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_goal' => BodyGoal::MUSCLE_GAIN,
        ]);
    }

    /**
     * Indicate that the profile has a weight loss goal.
     */
    public function weightLoss(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_goal' => BodyGoal::WEIGHT_LOSS,
        ]);
    }

    /**
     * Indicate that the profile is for a beginner.
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_level' => SkillLevel::BEGINNER,
        ]);
    }

    /**
     * Indicate that the profile trains at a gym.
     */
    public function gym(): static
    {
        return $this->state(fn (array $attributes) => [
            'training_place' => TrainingPlace::GYM,
        ]);
    }
}

