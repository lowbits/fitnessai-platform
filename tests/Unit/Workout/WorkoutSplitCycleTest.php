<?php

use App\Ai\Support\WorkoutSplit;
use App\Jobs\GenerateUserWorkoutPlan;

it('cycles the split across actual training sessions, covering upper and lower every week', function () {
    $indices = GenerateUserWorkoutPlan::trainingDayIndices(4);

    // 4x/week trains on days 1,3,5,7 (week 1) and 8,10,12,14 (week 2).
    $week1 = array_map(fn ($day) => GenerateUserWorkoutPlan::workoutNumberInCycle($day, 4, $indices), [1, 3, 5, 7]);
    $week2 = array_map(fn ($day) => GenerateUserWorkoutPlan::workoutNumberInCycle($day, 4, $indices), [8, 10, 12, 14]);

    // 1 = Upper A, 2 = Lower A, 3 = Upper B, 4 = Lower B. The old code gave [1,3,1,3] (all upper).
    expect($week1)->toBe([1, 2, 3, 4])
        ->and($week2)->toBe([1, 2, 3, 4]);
});

it('cycles a 3x/week plan through push, pull and legs', function () {
    $indices = GenerateUserWorkoutPlan::trainingDayIndices(3);

    $week1 = array_map(fn ($day) => GenerateUserWorkoutPlan::workoutNumberInCycle($day, 3, $indices), [1, 3, 5]);

    expect($week1)->toBe([1, 2, 3]);
});

it('respects explicit training days when counting the cycle', function () {
    $indices = GenerateUserWorkoutPlan::trainingDayIndices(4, ['monday', 'tuesday', 'thursday', 'friday']);

    // Training on days 1,2,4,5 → still four distinct sessions → 1,2,3,4.
    $sessions = array_map(fn ($day) => GenerateUserWorkoutPlan::workoutNumberInCycle($day, 4, $indices), [1, 2, 4, 5]);

    expect($sessions)->toBe([1, 2, 3, 4]);
});

it('resolves the 4x cycle to a split that actually covers lower body', function () {
    $labels = array_map(fn ($n) => WorkoutSplit::focus(4, $n)['label'], [1, 2, 3, 4]);

    expect($labels)->toBe(['Upper Body A', 'Lower Body A', 'Upper Body B', 'Lower Body B'])
        ->and(collect($labels)->filter(fn ($l) => str_contains($l, 'Lower'))->count())->toBe(2);
});

it('gives a seven-day plan a real split, not full body every day', function () {
    $split = WorkoutSplit::forFrequency(7);

    expect($split)->toHaveCount(7)
        ->and(collect($split)->pluck('label'))->toContain('Legs');
});

it('resets the cycle to the real training-day count for a mismatched custom schedule', function () {
    // 4 sessions configured but only 3 custom days: effective frequency is 3.
    $indices = GenerateUserWorkoutPlan::trainingDayIndices(4, ['monday', 'wednesday', 'friday']);
    $frequency = count($indices);

    $sessions = array_map(fn ($day) => GenerateUserWorkoutPlan::workoutNumberInCycle($day, $frequency, $indices), [1, 3, 5, 8]);

    // Days 1,3,5 = sessions 1,2,3; the next Monday (day 8) wraps back to 1, not a phantom 4.
    expect($frequency)->toBe(3)
        ->and($sessions)->toBe([1, 2, 3, 1]);
});

it('dedupes duplicate custom training days', function () {
    expect(GenerateUserWorkoutPlan::trainingDayIndices(2, ['monday', 'monday', 'friday']))->toBe([0, 4]);
});
