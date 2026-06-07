<?php

namespace Database\Factories;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietaryPreference;
use App\Enums\DietStyle;
use App\Enums\Gender;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'age' => fake()->numberBetween(18, 65),
            'gender' => fake()->randomElement(Gender::cases()),
            'weight_kg' => fake()->randomFloat(1, 50, 120),
            'height_cm' => fake()->randomFloat(1, 150, 200),
            'body_goal' => fake()->randomElement(BodyGoal::current()),
            'skill_level' => fake()->randomElement(SkillLevel::cases()),
            'activity_level' => fake()->randomElement(ActivityLevel::cases()),
            'training_place' => fake()->randomElement(TrainingPlace::cases()),
            'dietary_preference' => fake()->randomElement(DietaryPreference::cases()),
            'training_sessions_per_week' => fake()->numberBetween(1, 7),
        ];
    }

    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => Gender::MALE,
        ]);
    }

    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => Gender::FEMALE,
        ]);
    }

    public function buildMuscle(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_goal' => BodyGoal::BUILD_MUSCLE,
        ]);
    }

    public function loseWeight(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_goal' => BodyGoal::LOSE_WEIGHT,
        ]);
    }

    public function getFit(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_goal' => BodyGoal::GET_FIT,
        ]);
    }

    /** @deprecated Use buildMuscle() */
    public function muscleGain(): static
    {
        return $this->buildMuscle();
    }

    /** @deprecated Use loseWeight() */
    public function weightLoss(): static
    {
        return $this->loseWeight();
    }

    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'skill_level' => SkillLevel::BEGINNER,
        ]);
    }

    public function gym(): static
    {
        return $this->state(fn (array $attributes) => [
            'training_place' => TrainingPlace::GYM,
        ]);
    }

    public function withDietStyle(?DietStyle $dietStyle): static
    {
        return $this->state(function (array $attributes) use ($dietStyle) {
            return [
                'diet_style' => $dietStyle ?? fake()->randomElement(DietStyle::cases()),
            ];
        });
    }
}
