<?php

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietaryPreference;
use App\Enums\Gender;
use App\Helpers\Metabolism;

/*
|--------------------------------------------------------------------------
| Common carb/fat ratios used across tests.
| Defined here so tests are independent of enum implementations.
|--------------------------------------------------------------------------
*/

const RATIO_OMNIVORE = ['carbs' => 60, 'fat' => 40];
const RATIO_VEGAN = ['carbs' => 65, 'fat' => 35];
const RATIO_KETO = ['carbs' => 7, 'fat' => 93];

describe('Metabolism Helper', function () {

    /*
    |----------------------------------------------------------------------
    | BMR — Mifflin-St Jeor
    |----------------------------------------------------------------------
    */

    describe('BMR Calculation', function () {

        it('calculates correct BMR for male', function () {
            $bmr = Metabolism::calculateBMR(Gender::MALE, 28, 180.0, 80.0);
            expect($bmr)->toBe(1790);
        });

        it('calculates correct BMR for female', function () {
            $bmr = Metabolism::calculateBMR(Gender::FEMALE, 25, 165.0, 60.0);
            expect($bmr)->toBe(1345);
        });

        it('always returns a positive value', function () {
            expect(Metabolism::calculateBMR(Gender::MALE, 13, 150.0, 45.0))->toBeGreaterThan(0)
                ->and(Metabolism::calculateBMR(Gender::FEMALE, 80, 160.0, 70.0))->toBeGreaterThan(0);
        });

    });

    /*
    |----------------------------------------------------------------------
    | Body Composition Estimation — Deurenberg
    |----------------------------------------------------------------------
    */

    describe('Body Fat Estimation', function () {

        it('estimates higher BF% for females than males at same BMI', function () {
            $male = Metabolism::estimateBodyFatPercent(Gender::MALE, 30, 180.0, 80.0);
            $female = Metabolism::estimateBodyFatPercent(Gender::FEMALE, 30, 180.0, 80.0);

            expect($female)->toBeGreaterThan($male);
        });

        it('estimates higher BF% with age', function () {
            $young = Metabolism::estimateBodyFatPercent(Gender::MALE, 20, 180.0, 80.0);
            $older = Metabolism::estimateBodyFatPercent(Gender::MALE, 50, 180.0, 80.0);

            expect($older)->toBeGreaterThan($young);
        });

        it('clamps between 5% and 60%', function () {
            $low = Metabolism::estimateBodyFatPercent(Gender::MALE, 18, 200.0, 60.0);
            $high = Metabolism::estimateBodyFatPercent(Gender::FEMALE, 80, 150.0, 120.0);

            expect($low)->toBeGreaterThanOrEqual(5.0)
                ->and($high)->toBeLessThanOrEqual(60.0);
        });

        it('estimates lean mass correctly for Sarah persona', function () {
            // Sarah: female, 30y, 178cm, 76.5kg
            $bf = Metabolism::estimateBodyFatPercent(Gender::FEMALE, 30, 178.0, 76.5);
            $lean = Metabolism::estimateLeanMass(Gender::FEMALE, 30, 178.0, 76.5);

            expect($bf)->toBeBetween(28.0, 33.0)
                ->and($lean)->toBeBetween(51.0, 56.0);
        });

    });

    /*
    |----------------------------------------------------------------------
    | Training Calories — MET-based, weight-scaled
    |----------------------------------------------------------------------
    */

    describe('Training Calories', function () {

        it('returns zero without training', function () {
            expect(Metabolism::trainingCalories(0, 80.0))->toBe(0);
        });

        it('scales with body weight', function () {
            expect(Metabolism::trainingCalories(5, 55.0))->toBe(196)
                ->and(Metabolism::trainingCalories(5, 100.0))->toBe(357);
        });

        it('scales with sessions per week', function () {
            expect(Metabolism::trainingCalories(2, 80.0))->toBe(114)
                ->and(Metabolism::trainingCalories(6, 80.0))->toBe(343);
        });

        it('clamps at 7 sessions per week', function () {
            expect(Metabolism::trainingCalories(10, 80.0))
                ->toBe(Metabolism::trainingCalories(7, 80.0));
        });

    });

    /*
    |----------------------------------------------------------------------
    | TDEE — Base Activity + Training
    |----------------------------------------------------------------------
    */

    describe('TDEE Calculation', function () {

        it('equals base TDEE without training', function () {
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::MAINLY_SITTING, 0, 80.0);
            expect($tdee)->toBe(2148);
        });

        it('adds training calories for active trainee', function () {
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::MAINLY_SITTING, 5, 80.0);
            expect($tdee)->toBe(2434);
        });

        it('combines physical job with training', function () {
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::HARD_WORKING, 3, 80.0);
            expect($tdee)->toBe(3259);
        });

        test('each activity level produces a distinct result', function () {
            $results = collect(ActivityLevel::cases())
                ->map(fn ($level) => Metabolism::calculateTDEE(1800, $level, 0, 80.0))
                ->unique();

            expect($results)->toHaveCount(count(ActivityLevel::cases()));
        });

    });

    /*
    |----------------------------------------------------------------------
    | Daily Calorie Target — TDEE ± Goal Adjustment
    |----------------------------------------------------------------------
    */

    describe('Daily Calorie Target', function () {

        it('adds surplus for muscle gain', function () {
            expect(Metabolism::calculateDailyCalories(2434, BodyGoal::MUSCLE_GAIN))
                ->toBe(2734);
        });

        it('subtracts deficit for weight loss', function () {
            expect(Metabolism::calculateDailyCalories(2434, BodyGoal::WEIGHT_LOSS))
                ->toBe(1934);
        });

        it('keeps TDEE for maintenance', function () {
            expect(Metabolism::calculateDailyCalories(2434, BodyGoal::MAINTENANCE))
                ->toBe(2434);
        });

    });

    /*
    |----------------------------------------------------------------------
    | Macro Distribution
    |----------------------------------------------------------------------
    */

    describe('Macro Distribution', function () {

        it('anchors protein to body weight for muscle gain (unchanged)', function () {
            $macros = Metabolism::calculateMacros(2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            // 80kg × 2.0 g/kg = 160g
            expect($macros->proteinGrams)->toBe(160)
                ->and($macros->proteinChallenging)->toBeFalse();
        });

        it('uses lean mass for weight loss protein when demographics provided', function () {
            // 80kg male, 28y, 180cm
            // BF%: ~19.5%, lean: ~64.4kg, protein: 64.4 × 2.5 = 161g
            $macros = Metabolism::calculateMacros(
                1934, 80.0, BodyGoal::WEIGHT_LOSS, RATIO_OMNIVORE,
                gender: Gender::MALE, age: 28, height: 180.0,
            );

            expect($macros->proteinGrams)->toBeBetween(155, 170)
                ->and($macros->proteinChallenging)->toBeFalse();
        });

        it('caps protein at 35% of calories when demographics missing', function () {
            // Without demographics, falls back to total weight: 80 × 2.5 = 200g
            // Cap: 1934 × 0.35 / 4 = 169g
            $macros = Metabolism::calculateMacros(1934, 80.0, BodyGoal::WEIGHT_LOSS, RATIO_OMNIVORE);

            expect($macros->proteinGrams)->toBe(169);
        });

        it('produces much lower protein for Sarah with lean mass', function () {
            // Sarah: female, 30y, 178cm, 76.5kg, lose_weight, 2093 kcal
            // Lean mass: ~53.2kg, protein: 53.2 × 2.5 = 133g (~25% of calories)
            $macros = Metabolism::calculateMacros(
                2093, 76.5, BodyGoal::WEIGHT_LOSS, RATIO_OMNIVORE,
                gender: Gender::FEMALE, age: 30, height: 178.0,
            );

            expect($macros->proteinGrams)->toBeBetween(125, 140)
                ->and($macros->proteinChallenging)->toBeFalse()
                ->and($macros->minimumFatEnforced)->toBeFalse();
        });

        it('distributes remaining calories by carb/fat ratio', function () {
            $macros = Metabolism::calculateMacros(2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            expect($macros->carbsGrams)->toBe(314)
                ->and($macros->fatGrams)->toBe(93);
        });

        it('handles ketogenic ratios correctly', function () {
            $macros = Metabolism::calculateMacros(2434, 80.0, BodyGoal::MAINTENANCE, RATIO_KETO);

            expect($macros->proteinGrams)->toBe(144)
                ->and($macros->carbsGrams)->toBe(33)
                ->and($macros->fatGrams)->toBe(192);
        });

        it('enforces minimum fat for hormonal health', function () {
            // 60kg female, 25y, 165cm, vegan, weight loss
            // Lean mass: ~43.9kg, protein: 43.9 × 2.5 = 110g
            $macros = Metabolism::calculateMacros(
                1478, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN,
                gender: Gender::FEMALE, age: 25, height: 165.0,
            );

            expect($macros->fatGrams)->toBeGreaterThanOrEqual(48) // 60 × 0.8
                ->and($macros->minimumFatEnforced)->toBeTrue(); // vegan 65:35 ratio still under-fats at low calories
        });

        it('does not flag when protein is comfortably within range', function () {
            $macros = Metabolism::calculateMacros(2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            expect($macros->proteinChallenging)->toBeFalse()
                ->and($macros->minimumFatEnforced)->toBeFalse();
        });

        it('produces calories within rounding tolerance of target', function () {
            $scenarios = [
                [2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE, null, null, null],
                [1478, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN, Gender::FEMALE, 25, 165.0],
                [2434, 80.0, BodyGoal::MAINTENANCE, RATIO_KETO, null, null, null],
                [2093, 76.5, BodyGoal::WEIGHT_LOSS, RATIO_OMNIVORE, Gender::FEMALE, 30, 178.0],
            ];

            foreach ($scenarios as [$calories, $weight, $goal, $ratio, $gender, $age, $height]) {
                $macros = Metabolism::calculateMacros($calories, $weight, $goal, $ratio, $gender, $age, $height);

                expect($macros->totalCalories())
                    ->toBeBetween($calories - 50, $calories + 50);
            }
        });

    });

    /*
    |----------------------------------------------------------------------
    | Full Calculation Chain — End-to-End Scenarios
    |----------------------------------------------------------------------
    */

    describe('Full Calculation Chain', function () {

        it('produces correct plan for male muscle gain', function () {
            $bmr = Metabolism::calculateBMR(Gender::MALE, 30, 186.0, 84.0);
            expect($bmr)->toBe(1858);

            $tdee = Metabolism::calculateTDEE($bmr, ActivityLevel::MAINLY_SITTING, 5, 84.0);
            expect($tdee)->toBe(2530);

            $target = Metabolism::calculateDailyCalories($tdee, BodyGoal::MUSCLE_GAIN);
            expect($target)->toBe(2830);

            $macros = Metabolism::calculateMacros($target, 84.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            expect($macros->proteinGrams)->toBe(168)        // 84 × 2.0 (total weight, not lean)
                ->and($macros->proteinChallenging)->toBeFalse()
                ->and($macros->minimumFatEnforced)->toBeFalse()
                ->and($macros->totalCalories())->toBeBetween(2780, 2880);
        });

        it('produces safe plan for female vegan weight loss with lean mass', function () {
            $bmr = Metabolism::calculateBMR(Gender::FEMALE, 25, 165.0, 60.0);
            expect($bmr)->toBe(1345);

            $tdee = Metabolism::calculateTDEE($bmr, ActivityLevel::MAINLY_STANDING, 3, 60.0);
            expect($tdee)->toBe(1978);

            $target = Metabolism::calculateDailyCalories($tdee, BodyGoal::WEIGHT_LOSS);
            expect($target)->toBe(1478);

            $macros = Metabolism::calculateMacros(
                $target, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN,
                gender: Gender::FEMALE, age: 25, height: 165.0,
            );

            // Lean mass: ~43.9kg × 2.5 = ~110g (not 150g from total weight)
            expect($macros->proteinGrams)->toBeBetween(105, 115)
                ->and($macros->fatGrams)->toBeGreaterThanOrEqual(48) // 60 × 0.8
                ->and($macros->totalCalories())->toBeBetween(1428, 1528);
        });

        it('resolves macros through enum carbFatRatio methods', function () {
            expect(DietaryPreference::OMNIVORE->carbFatRatio())->toBe(RATIO_OMNIVORE)
                ->and(DietaryPreference::VEGAN->carbFatRatio())->toBe(RATIO_VEGAN);
        });

    });

});
