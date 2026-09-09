<?php

use App\Support\PlanEvaluator\DayAggregator;

function aggregate(array $facts): array
{
    return (new DayAggregator)->aggregate($facts);
}

it('sums weekly sets and counts frequency across days', function () {
    $facts = aggregate([
        'plan_type' => 'workout',
        'days' => [
            ['label' => 'Push', 'groups' => [['group' => 'chest', 'sets' => 3], ['group' => 'legs', 'sets' => 5]]],
            ['label' => 'Pull', 'groups' => [['group' => 'back', 'sets' => 6]]],
            ['label' => 'Push 2', 'groups' => [['group' => 'chest', 'sets' => 3]]],
        ],
    ]);

    $chest = collect($facts['muscle_groups'])->firstWhere('group', 'chest');
    $legs = collect($facts['muscle_groups'])->firstWhere('group', 'legs');

    expect($facts['days_per_week'])->toBe(3)
        ->and($chest['weekly_sets'])->toBe(6)
        ->and($chest['sessions_per_week'])->toBe(2)
        ->and($legs['weekly_sets'])->toBe(5)
        ->and($legs['sessions_per_week'])->toBe(1);
});

it('scales sets and frequency by times_per_week for repeated sessions', function () {
    $facts = aggregate([
        'days' => [
            ['label' => 'Upper', 'times_per_week' => 2, 'groups' => [['group' => 'chest', 'sets' => 3], ['group' => 'back', 'sets' => 5]]],
            ['label' => 'Lower', 'times_per_week' => 2, 'groups' => [['group' => 'legs', 'sets' => 8]]],
        ],
    ]);

    $chest = collect($facts['muscle_groups'])->firstWhere('group', 'chest');
    $legs = collect($facts['muscle_groups'])->firstWhere('group', 'legs');

    expect($facts['days_per_week'])->toBe(4)
        ->and($chest['weekly_sets'])->toBe(6)
        ->and($chest['sessions_per_week'])->toBe(2)
        ->and($legs['weekly_sets'])->toBe(16)
        ->and($legs['sessions_per_week'])->toBe(2);
});

it('counts a muscle trained twice in one day as one session', function () {
    $facts = aggregate([
        'days' => [
            ['label' => 'Full body', 'groups' => [['group' => 'legs', 'sets' => 4], ['group' => 'legs', 'sets' => 4]]],
        ],
    ]);

    $legs = collect($facts['muscle_groups'])->firstWhere('group', 'legs');

    expect($legs['weekly_sets'])->toBe(8)
        ->and($legs['sessions_per_week'])->toBe(1);
});

it('leaves facts without days untouched', function () {
    expect(aggregate(['plan_type' => 'nutrition']))->toBe(['plan_type' => 'nutrition']);
});
