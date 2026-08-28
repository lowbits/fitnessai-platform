<?php

use App\Support\MealSlotBudget;

function totalKcal(array $occasions): int
{
    return array_sum(array_column($occasions, 'kcal'));
}

it('leaves a low-calorie day as plain meals with no booster', function () {
    $occasions = MealSlotBudget::compose(['breakfast', 'lunch', 'dinner'], 2000, autoFill: true);

    expect($occasions)->toHaveCount(3)
        ->and(collect($occasions)->pluck('booster')->every(fn ($b) => $b === false))->toBeTrue()
        ->and(collect($occasions)->max('kcal'))->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL);
});

it('caps mains and adds a booster on a high-calorie day', function () {
    $occasions = MealSlotBudget::compose(['breakfast', 'lunch', 'dinner'], 2785, autoFill: true);

    $mains = collect($occasions)->where('booster', false);
    $boosters = collect($occasions)->where('booster', true);

    expect($mains->max('kcal'))->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and($boosters)->toHaveCount(1)
        ->and($boosters->first()['type'])->toBe('flex')
        ->and($boosters->max('kcal'))->toBeLessThanOrEqual(MealSlotBudget::BOOSTER_MAX_KCAL);
});

it('keeps every booster within the booster ceiling on a very high day', function () {
    $occasions = MealSlotBudget::compose(['breakfast', 'lunch', 'dinner'], 3400, autoFill: true);

    expect(collect($occasions)->where('booster', false)->max('kcal'))->toBeLessThanOrEqual(MealSlotBudget::MAIN_CAP_KCAL)
        ->and(collect($occasions)->where('booster', true)->max('kcal'))->toBeLessThanOrEqual(MealSlotBudget::BOOSTER_MAX_KCAL)
        ->and(collect($occasions)->where('booster', true)->count())->toBeGreaterThanOrEqual(1);
});

it('preserves the daily calorie total when boosters absorb the overflow', function (int $daily) {
    $occasions = MealSlotBudget::compose(['breakfast', 'lunch', 'dinner'], $daily, autoFill: true);

    // Booster kcal exactly restores what capping removed, so the total is unchanged
    // (bar the ±1-2 kcal rounding already present in the share split).
    expect(abs(totalKcal($occasions) - $daily))->toBeLessThanOrEqual(2);
})->with([2000, 2400, 2785, 3200, 3400]);

it('never adds a booster when auto-fill is off, even with oversized mains', function () {
    $occasions = MealSlotBudget::compose(['breakfast', 'lunch', 'dinner'], 3200, autoFill: false);

    expect($occasions)->toHaveCount(3)
        ->and(collect($occasions)->pluck('booster')->every(fn ($b) => $b === false))->toBeTrue()
        ->and(collect($occasions)->max('kcal'))->toBeGreaterThan(MealSlotBudget::MAIN_CAP_KCAL);
});
