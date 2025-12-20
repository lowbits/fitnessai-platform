<?php
// tests/Unit/Models/UserProfileTest.php

use App\Models\UserProfile;
use App\Enums\Gender;
use App\Enums\BodyGoal;
use App\Enums\ActivityLevel;
use App\Enums\DietType;

describe('UserProfile Model (Unit)', function () {

    it('calculates bmr without database', function () {
        $profile = UserProfile::factory()->make([
            'gender' => Gender::MALE,
            'age' => 28,
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
        ]);

        expect($profile->calculateBMR())->toBe(1790);
    });

    it('calculates tdee without database', function () {
        $profile = UserProfile::factory()->make([
            'gender' => Gender::MALE,
            'age' => 28,
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
            'activity_level' => ActivityLevel::MAINLY_SITTING,
        ]);

        expect($profile->calculateTDEE())->toBe(2148);
    });

    it('calculates daily calories without database', function () {
        $profile = UserProfile::factory()->make([
            'gender' => Gender::MALE,
            'age' => 28,
            'weight_kg' => 80.0,
            'height_cm' => 180.0,
            'activity_level' => ActivityLevel::MAINLY_SITTING,
            'body_goal' => BodyGoal::MUSCLE_GAIN,
        ]);

        expect($profile->calculateDailyCalories())->toBe(2448);
    });

    it('calculates macros without database', function () {
        $profile = UserProfile::factory()->make([
            'gender' => Gender::MALE,
            'age' => 28,
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
