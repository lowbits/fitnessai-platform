<?php

use App\Support\MealSlotBudget;

it('leaves a low-calorie day uncapped with no fill budget', function () {
    $slots = ['breakfast', 'lunch', 'dinner'];
    $map = MealSlotBudget::mainSlotKcal($slots, 2000, autoFill: true);

    expect(collect($map)->max())->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and(MealSlotBudget::fillBudget($slots, 2000, autoFill: true))->toBeLessThanOrEqual(2);
});

it('caps the mains and leaves a fill budget on a high-calorie day', function () {
    $slots = ['breakfast', 'lunch', 'dinner'];
    $map = MealSlotBudget::mainSlotKcal($slots, 3400, autoFill: true);

    expect($map['breakfast'])->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and($map['lunch'])->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and($map['dinner'])->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        // 3400 − 3×800 capped mains = 1000 left for the AI to fill.
        ->and(MealSlotBudget::fillBudget($slots, 3400, autoFill: true))->toBe(1000);
});

it('leaves mains uncapped and no fill budget when auto-fill is off', function () {
    $slots = ['breakfast', 'lunch', 'dinner'];
    $map = MealSlotBudget::mainSlotKcal($slots, 3400, autoFill: false);

    expect(collect($map)->max())->toBeGreaterThan(MealSlotBudget::MAIN_CAP_KCAL)
        ->and(MealSlotBudget::fillBudget($slots, 3400, autoFill: false))->toBe(0);
});

it('never caps a user-selected snack, only cooked mains', function () {
    $map = MealSlotBudget::mainSlotKcal(['breakfast', 'lunch', 'snack', 'dinner'], 3400, autoFill: true);

    expect(array_keys($map))->toContain('snack')
        ->and($map['snack'])->toBeLessThan(MealSlotBudget::MAIN_CAP_KCAL);
});
