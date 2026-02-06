<?php

use App\Helpers\Metabolism;
use App\Enums\Gender;
use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietaryPreference;
use App\Enums\DietStyle;

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
            // (10×80) + (6.25×180) - (5×28) + 5 = 1790
            $bmr = Metabolism::calculateBMR(Gender::MALE, 28, 180.0, 80.0);

            expect($bmr)->toBe(1790);
        });

        it('calculates correct BMR for female', function () {
            // (10×60) + (6.25×165) - (5×25) - 161 = 1345
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
    | Training Calories — MET-based, weight-scaled
    |----------------------------------------------------------------------
    */

    describe('Training Calories', function () {

        it('returns zero without training', function () {
            expect(Metabolism::trainingCalories(0, 80.0))->toBe(0);
        });

        it('scales with body weight', function () {
            // 55kg × 5.0 × 5 / 7 = 196
            // 100kg × 5.0 × 5 / 7 = 357
            expect(Metabolism::trainingCalories(5, 55.0))->toBe(196)
                ->and(Metabolism::trainingCalories(5, 100.0))->toBe(357);
        });

        it('scales with sessions per week', function () {
            // 80kg × 5.0 × 2 / 7 = 114
            // 80kg × 5.0 × 6 / 7 = 343
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
            // 1790 × 1.2 + 0 = 2148
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::MAINLY_SITTING, 0, 80.0);

            expect($tdee)->toBe(2148);
        });

        it('adds training calories for active trainee', function () {
            // base: 1790 × 1.2 = 2148
            // training: 80 × 5.0 × 5 / 7 = 286
            // total: 2434
            $tdee = Metabolism::calculateTDEE(1790, ActivityLevel::MAINLY_SITTING, 5, 80.0);

            expect($tdee)->toBe(2434);
        });

        it('combines physical job with training', function () {
            // base: 1790 × 1.725 = 3088
            // training: 80 × 5.0 × 3 / 7 = 171
            // total: 3259
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

        it('anchors protein to body weight, not calories', function () {
            $macros = Metabolism::calculateMacros(2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            // 80kg × 2.0 g/kg = 160g
            expect($macros->proteinGrams)->toBe(160)
                ->and($macros->proteinChallenging)->toBeFalse();
        });

        it('uses higher protein during weight loss to preserve muscle', function () {
            $macros = Metabolism::calculateMacros(1934, 80.0, BodyGoal::WEIGHT_LOSS, RATIO_OMNIVORE);

            // 80kg × 2.5 g/kg = 200g (ISSN: 2.3–3.1 in deficit)
            expect($macros->proteinGrams)->toBe(200)
                ->and($macros->proteinChallenging)->toBeTrue();
        });

        it('distributes remaining calories by carb/fat ratio', function () {
            $macros = Metabolism::calculateMacros(2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            // Protein: 160g = 640 kcal
            // Remaining: 2094 kcal, ratio 60:40
            // Carbs: 2094 × 0.6 / 4 = 314g
            // Fat: 2094 × 0.4 / 9 = 93g
            expect($macros->carbsGrams)->toBe(314)
                ->and($macros->fatGrams)->toBe(93);
        });

        it('handles ketogenic ratios correctly', function () {
            $macros = Metabolism::calculateMacros(2434, 80.0, BodyGoal::MAINTENANCE, RATIO_KETO);

            // Protein: 80 × 1.8 = 144g = 576 kcal
            // Remaining: 1858, ratio 7:93
            // Carbs: 1858 × 0.07 / 4 = 33g
            // Fat: 1858 × 0.93 / 9 = 192g
            expect($macros->proteinGrams)->toBe(144)
                ->and($macros->carbsGrams)->toBe(33)
                ->and($macros->fatGrams)->toBe(192);
        });

        it('enforces minimum fat for hormonal health', function () {
            $macros = Metabolism::calculateMacros(1478, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN);

            // Protein: 150g = 600 kcal
            // Remaining: 878 kcal, ratio 65:35
            // Fat from ratio: 878 × 0.35 / 9 = 34g
            // Min fat: 60 × 0.8 = 48g → enforced!
            // Carbs adjusted: (878 - 432) / 4 = 112g
            expect($macros->fatGrams)->toBe(48)
                ->and($macros->minimumFatEnforced)->toBeTrue()
                ->and($macros->carbsGrams)->toBe(112);
        });

        it('enforces minimum fat even on omnivore deficit', function () {
            $macros = Metabolism::calculateMacros(1934, 80.0, BodyGoal::WEIGHT_LOSS, RATIO_OMNIVORE);

            // Protein: 200g = 800 kcal
            // Remaining: 1134 kcal, ratio 60:40
            // Fat from ratio: 1134 × 0.4 / 9 = 50g
            // Min fat: 80 × 0.8 = 64g → enforced!
            expect($macros->fatGrams)->toBe(64)
                ->and($macros->minimumFatEnforced)->toBeTrue();
        });

        it('flags protein as challenging when exceeding 35% of calories', function () {
            $macros = Metabolism::calculateMacros(1478, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN);

            // 150g × 4 = 600 kcal = 40.6% of 1478 → challenging
            expect($macros->proteinGrams)->toBe(150)
                ->and($macros->proteinChallenging)->toBeTrue();
        });

        it('does not flag when protein is comfortably within range', function () {
            $macros = Metabolism::calculateMacros(2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            // 160g × 4 = 640 kcal = 23.4% of 2734 → fine
            expect($macros->proteinChallenging)->toBeFalse()
                ->and($macros->minimumFatEnforced)->toBeFalse();
        });

        it('produces calories within rounding tolerance of target', function () {
            $scenarios = [
                [2734, 80.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE],
                [1478, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN],
                [2434, 80.0, BodyGoal::MAINTENANCE, RATIO_KETO],
            ];

            foreach ($scenarios as [$calories, $weight, $goal, $ratio]) {
                $macros = Metabolism::calculateMacros($calories, $weight, $goal, $ratio);

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
            // 84kg male, 30y, 186cm, desk job, 5×/week, omnivore
            $bmr = Metabolism::calculateBMR(Gender::MALE, 30, 186.0, 84.0);
            expect($bmr)->toBe(1858);

            $tdee = Metabolism::calculateTDEE($bmr, ActivityLevel::MAINLY_SITTING, 5, 84.0);
            // base: round(1858 × 1.2) = 2230, training: round(84 × 5.0 × 5 / 7) = 300
            expect($tdee)->toBe(2530);

            $target = Metabolism::calculateDailyCalories($tdee, BodyGoal::MUSCLE_GAIN);
            expect($target)->toBe(2830);

            $macros = Metabolism::calculateMacros($target, 84.0, BodyGoal::MUSCLE_GAIN, RATIO_OMNIVORE);

            expect($macros->proteinGrams)->toBe(168)        // 84 × 2.0
            ->and($macros->proteinChallenging)->toBeFalse()
                ->and($macros->minimumFatEnforced)->toBeFalse()
                ->and($macros->totalCalories())->toBeBetween(2780, 2880);
        });

        it('produces safe plan for female vegan weight loss', function () {
            // 60kg female, 25y, 165cm, standing job, 3×/week, vegan
            $bmr = Metabolism::calculateBMR(Gender::FEMALE, 25, 165.0, 60.0);
            expect($bmr)->toBe(1345);

            $tdee = Metabolism::calculateTDEE($bmr, ActivityLevel::MAINLY_STANDING, 3, 60.0);
            // base: round(1345 × 1.375) = 1849, training: round(60 × 5.0 × 3 / 7) = 129
            expect($tdee)->toBe(1978);

            $target = Metabolism::calculateDailyCalories($tdee, BodyGoal::WEIGHT_LOSS);
            expect($target)->toBe(1478);

            $macros = Metabolism::calculateMacros($target, 60.0, BodyGoal::WEIGHT_LOSS, RATIO_VEGAN);

            expect($macros->proteinGrams)->toBe(150)         // 60 × 2.5
            ->and($macros->proteinChallenging)->toBeTrue()
                ->and($macros->minimumFatEnforced)->toBeTrue()
                ->and($macros->fatGrams)->toBeGreaterThanOrEqual(48) // 60 × 0.8
                ->and($macros->totalCalories())->toBeBetween(1428, 1528);
        });

        it('resolves macros through enum carbFatRatio methods', function () {
            // Verify enum integration matches our test constants
            expect(DietaryPreference::OMNIVORE->carbFatRatio())->toBe(RATIO_OMNIVORE)
                ->and(DietaryPreference::VEGAN->carbFatRatio())->toBe(RATIO_VEGAN);
        });

    });

});
