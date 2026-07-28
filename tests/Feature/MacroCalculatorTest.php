<?php

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietaryPreference;
use App\Enums\Gender;
use App\Services\MacroCalculatorService;

describe('MacroCalculatorService', function () {
    it('drives results off the product Metabolism helper', function () {
        $result = app(MacroCalculatorService::class)->calculate(
            gender: Gender::FEMALE,
            age: 32,
            heightCm: 170,
            weightKg: 68,
            activity: ActivityLevel::MAINLY_WALKING,
            trainingSessions: 3,
            goal: BodyGoal::LOSE_WEIGHT,
            diet: DietaryPreference::OMNIVORE,
        );

        expect($result['calories'])->toBeGreaterThan(0)
            ->and($result['bmr'])->toBeGreaterThan(0)
            ->and($result['tdee'])->toBeGreaterThan($result['bmr'])
            ->and($result['protein']['grams'])->toBeGreaterThan(0)
            ->and($result['carbs']['grams'])->toBeGreaterThan(0)
            ->and($result['fat']['grams'])->toBeGreaterThan(0);
    });

    it('produces macro shares that sum to 1', function () {
        $result = app(MacroCalculatorService::class)->calculate(
            gender: Gender::MALE,
            age: 28,
            heightCm: 180,
            weightKg: 80,
            activity: ActivityLevel::MAINLY_SITTING,
            trainingSessions: 4,
            goal: BodyGoal::BUILD_MUSCLE,
            diet: DietaryPreference::OMNIVORE,
        );

        $shareSum = $result['protein']['share'] + $result['carbs']['share'] + $result['fat']['share'];
        expect($shareSum)->toBeGreaterThan(0.99)->toBeLessThan(1.01);
    });

    it('maps public goal values onto the BodyGoal enum', function () {
        $service = app(MacroCalculatorService::class);
        expect($service->resolveGoal('lose'))->toBe(BodyGoal::LOSE_WEIGHT)
            ->and($service->resolveGoal('gain'))->toBe(BodyGoal::BUILD_MUSCLE)
            ->and($service->resolveGoal('maintain'))->toBe(BodyGoal::GET_FIT);
    });

    it('computes from a raw string input array (the reload payload)', function () {
        $result = app(MacroCalculatorService::class)->calculateFromArray([
            'gender' => 'female',
            'age' => '32',
            'height' => '170',
            'weight' => '68',
            'activity' => 'mainly_walking',
            'sessions' => '3',
            'goal' => 'lose',
            'diet' => 'omnivore',
        ]);

        expect($result)->toHaveKeys(['bmr', 'tdee', 'calories', 'protein', 'carbs', 'fat'])
            ->and($result['calories'])->toBeGreaterThan(0);
    });
});
