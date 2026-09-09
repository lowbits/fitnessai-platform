<?php

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
