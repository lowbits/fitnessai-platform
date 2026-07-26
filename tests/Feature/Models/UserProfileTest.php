<?php
use App\Enums\DietaryPreference;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Models\UserProfile;
use App\Enums\Gender;
use App\Enums\BodyGoal;
use App\Enums\ActivityLevel;
use App\Enums\DietType;

describe('UserProfile Model (Unit)', function () {

    it('calculates bmr without database', function () {
        $profile = UserProfile::factory()->create([
            'gender' => Gender::MALE,
            'birthdate' => now()->subYears(28),
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
        ]);

        expect($profile->calculateBMR())->toBe(1790);
    });

    it('calculates tdee without database', function () {
        $profile = UserProfile::factory()->create([
            'gender' => Gender::MALE,
            'birthdate' => now()->subYears(28),
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
            'activity_level' => ActivityLevel::MAINLY_SITTING,
            'skill_level' => SkillLevel::BEGINNER,
            'training_place' => TrainingPlace::GYM,
            'dietary_preference' => DietaryPreference::OMNIVORE,
            'training_sessions_per_week' => 3,
        ]);

        expect($profile->calculateTDEE())->toBe(2319);
    });

    it('calculates daily calories without database', function () {
        $profile = UserProfile::factory()->create([
            'gender' => Gender::MALE,
            'birthdate' => now()->subYears(28),
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
            'activity_level' => ActivityLevel::MAINLY_SITTING,
            'skill_level' => SkillLevel::ADVANCED,
            'training_place' => TrainingPlace::HOME,
            'dietary_preference' => DietaryPreference::VEGAN,
            'body_goal' => BodyGoal::MUSCLE_GAIN,
            'training_sessions_per_week' => 5,
        ]);

        expect($profile->calculateDailyCalories())->toBe(2734);
    });

    it('calculates macros without database', function () {
        $profile = UserProfile::factory()->create([
            'gender' => Gender::MALE,
            'birthdate' => now()->subYears(28),
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
            'activity_level' => ActivityLevel::MAINLY_SITTING,
            'body_goal' => BodyGoal::MUSCLE_GAIN,
            'diet_type' => DietType::HIGH_PROTEIN,
        ]);

        $macros = $profile->calculateMacros();

        expect($macros)
            ->toHaveKeys(['protein_g', 'carbs_g', 'fat_g']);
    });

});
