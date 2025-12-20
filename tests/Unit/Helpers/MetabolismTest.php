<?php
// tests/Unit/Helpers/MetabolismTest.php

use App\Helpers\Metabolism;
use App\Enums\Gender;
use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;

describe('Metabolism Helper', function () {

    describe('BMR Calculation', function () {

        it('calculates correct bmr for male', function () {
            $bmr = Metabolism::calculateBMR(
                Gender::MALE,
                28,
                180.0,
                80.0
            );

            // (10*80) + (6.25*180) - (5*28) + 5 = 1790
            expect($bmr)->toBe(1790);
        });

        it('calculates correct bmr for female', function () {
            $bmr = Metabolism::calculateBMR(
                Gender::FEMALE,
                25,
                165.0,
                60.0
            );

            // (10*60) + (6.25*165) - (5*25) - 161 = 1345
            expect($bmr)->toBe(1345);
        });

        it('handles edge case very young age', function () {
            $bmr = Metabolism::calculateBMR(
                Gender::MALE,
                13,
                150.0,
                45.0
            );

            expect($bmr)->toBeGreaterThan(0);
        });

        it('handles edge case very old age', function () {
            $bmr = Metabolism::calculateBMR(
                Gender::FEMALE,
                80,
                160.0,
                70.0
            );

            expect($bmr)->toBeGreaterThan(0);
        });

    });

    describe('TDEE Calculation', function () {

        it('calculates correct tdee for sedentary', function () {
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::MAINLY_SITTING);

            // 1790 * 1.2 = 2148
            expect($tdee)->toBe(2148);
        });

        it('calculates correct tdee for very active', function () {
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::HARD_WORKING);

            // 1790 * 1.725 = 3088
            expect($tdee)->toBe(3088);
        });

        test('all activity levels produce different results', function () {
            $bmr = 1800;

            $results = collect(ActivityLevel::cases())->map(fn($level) =>
            Metabolism::calculateTDEE($bmr, $level)
            )->unique();

            expect($results->count())->toBe(count(ActivityLevel::cases()));
        });

    });

    describe('Daily Calories Calculation', function () {

        it('adds calories for muscle gain', function () {
            $calories = Metabolism::calculateDailyCalories(2148, BodyGoal::MUSCLE_GAIN);

            // 2148 + 300 = 2448
            expect($calories)->toBe(2448);
        });

        it('subtracts calories for weight loss', function () {
            $calories = Metabolism::calculateDailyCalories(2148, BodyGoal::WEIGHT_LOSS);

            // 2148 - 500 = 1648
            expect($calories)->toBe(1648);
        });

        it('maintains calories for maintenance', function () {
            $calories = Metabolism::calculateDailyCalories(2148, BodyGoal::MAINTENANCE);

            expect($calories)->toBe(2148);
        });

    });

    describe('Macros Calculation', function () {

        it('calculates correct macros for high protein diet', function () {
            $macros = Metabolism::calculateMacros(2400, DietType::HIGH_PROTEIN);

            // High Protein: 40% protein, 35% carbs, 25% fat
            // Protein: (2400 * 0.40) / 4 = 240g
            // Carbs: (2400 * 0.35) / 4 = 210g
            // Fat: (2400 * 0.25) / 9 = 67g

            expect($macros)
                ->toHaveKey('protein_g', 240)
                ->toHaveKey('carbs_g', 210)
                ->toHaveKey('fat_g', 67);
        });

        it('calculates correct macros for keto diet', function () {
            $macros = Metabolism::calculateMacros(2000, DietType::KETOGENIC);

            // Keto: 25% protein, 5% carbs, 70% fat
            // Protein: (2000 * 0.25) / 4 = 125g
            // Carbs: (2000 * 0.05) / 4 = 25g
            // Fat: (2000 * 0.70) / 9 = 156g

            expect($macros)
                ->toHaveKey('protein_g', 125)
                ->toHaveKey('carbs_g', 25)
                ->toHaveKey('fat_g', 156);
        });

        it('macros add up to approximately total calories', function () {
            $calories = 2500;
            $macros = Metabolism::calculateMacros($calories, DietType::OMNIVORE);

            $totalCalories = ($macros['protein_g'] * 4)
                + ($macros['carbs_g'] * 4)
                + ($macros['fat_g'] * 9);

            // Allow small rounding difference
            expect($totalCalories)->toBeBetween($calories - 50, $calories + 50);
        });

    });

});
