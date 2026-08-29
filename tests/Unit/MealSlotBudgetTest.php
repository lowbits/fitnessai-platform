<?php

use App\Support\MealSlotBudget;

it('splits a normal day across the slots with no flex shake', function () {
    $map = MealSlotBudget::slotKcal(['breakfast', 'lunch', 'dinner'], 2000, autoFill: true);

    expect($map)->not->toHaveKey('flex')
        ->and(collect($map)->max())->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and(abs(array_sum($map) - 2000))->toBeLessThanOrEqual(2);
});

it('adds a snack, caps the meals and adds a flex shake on a high-calorie day', function () {
    $map = MealSlotBudget::slotKcal(['breakfast', 'lunch', 'dinner'], 3977, autoFill: true);

    expect($map)->toHaveKey('snack')
        ->toHaveKey('flex')
        ->and($map['breakfast'])->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and($map['lunch'])->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and($map['dinner'])->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        // The added snack shrinks the shake relative to a no-snack day.
        ->and($map['flex'])->toBeLessThan($map['dinner'])
        // Meals + snack + flex must still sum to the daily target.
        ->and(abs(array_sum($map) - 3977))->toBeLessThanOrEqual(2);
});

it('does not add a snack below the snack threshold', function () {
    $map = MealSlotBudget::slotKcal(['breakfast', 'lunch', 'dinner'], 2500, autoFill: true);

    expect($map)->not->toHaveKey('snack')
        ->and(array_keys($map))->toEqualCanonicalizing(['breakfast', 'lunch', 'dinner']);
});

it('leaves meals uncapped with no flex when auto-fill is off', function () {
    $map = MealSlotBudget::slotKcal(['breakfast', 'lunch', 'dinner'], 3977, autoFill: false);

    expect($map)->not->toHaveKey('flex')
        ->and(collect($map)->max())->toBeGreaterThan(MealSlotBudget::MAIN_CAP_KCAL);
});
