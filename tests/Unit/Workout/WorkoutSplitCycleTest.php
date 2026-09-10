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

/**
 * Map the split's granular muscles to our six major groups and count how many
 * days per week train each. This lets us prove a split is balanced without
 * generating a single AI workout.
 *
 * @return array<string, int>
 */
function groupFrequency(int $frequency): array
{
    $map = [
        'quadriceps' => 'legs', 'hamstrings' => 'legs', 'glutes' => 'legs', 'calves' => 'legs',
        'chest' => 'chest',
        'back' => 'back', 'upper_back' => 'back', 'lower_back' => 'back', 'lats' => 'back',
        'shoulders' => 'shoulders', 'rear_delts' => 'shoulders', 'rotator_cuff' => 'shoulders',
        'biceps' => 'arms', 'triceps' => 'arms', 'forearms' => 'arms',
        'core' => 'core',
    ];

    $counts = [];

    foreach (WorkoutSplit::forFrequency($frequency) as $day) {
        $groups = collect(explode(',', $day['muscles']))
            ->map(fn (string $muscle) => $map[trim($muscle)] ?? null)
            ->filter()
            ->unique();

        foreach ($groups as $group) {
            $counts[$group] = ($counts[$group] ?? 0) + 1;
        }
    }

    return $counts;
}

it('covers every major muscle group at each frequency', function (int $frequency) {
    $counts = groupFrequency($frequency);

    expect(array_keys($counts))->toContain('legs', 'back', 'chest', 'shoulders', 'arms', 'core');
})->with([2, 3, 4, 5, 6, 7]);

it('trains every major group at least twice a week', function (int $frequency) {
    $counts = groupFrequency($frequency);
    $min = collect(['legs', 'back', 'chest', 'shoulders', 'arms', 'core'])
        ->map(fn (string $group) => $counts[$group] ?? 0)
        ->min();

    expect($min)->toBeGreaterThanOrEqual(2);
})->with([2, 3, 4, 5, 6]);

/**
 * Count weekly sessions per individual muscle, without collapsing legs into one
 * "legs" group. This is what proves the ≥2x/muscle claim the broad-group test
 * cannot: it would catch calves being omitted or quads landing on only one day.
 *
 * @return array<string, int>
 */
function muscleFrequency(int $frequency): array
{
    $canonical = [
        'quadriceps' => 'quadriceps', 'hamstrings' => 'hamstrings', 'glutes' => 'glutes', 'calves' => 'calves',
        'chest' => 'chest',
        'back' => 'back', 'upper_back' => 'back', 'lower_back' => 'back', 'lats' => 'back',
        'shoulders' => 'shoulders', 'rear_delts' => 'shoulders', 'rotator_cuff' => 'shoulders',
        'biceps' => 'biceps', 'triceps' => 'triceps',
        'core' => 'core',
    ];

    $counts = [];

    foreach (WorkoutSplit::forFrequency($frequency) as $day) {
        $muscles = collect(explode(',', $day['muscles']))
            ->map(fn (string $muscle) => $canonical[trim($muscle)] ?? null)
            ->filter()
            ->unique();

        foreach ($muscles as $muscle) {
            $counts[$muscle] = ($counts[$muscle] ?? 0) + 1;
        }
    }

    return $counts;
}

it('trains every individual major muscle at least twice a week', function (int $frequency) {
    $counts = muscleFrequency($frequency);
    $min = collect(['quadriceps', 'hamstrings', 'glutes', 'calves', 'chest', 'back', 'shoulders', 'biceps', 'triceps', 'core'])
        ->map(fn (string $muscle) => $counts[$muscle] ?? 0)
        ->min();

    expect($min)->toBeGreaterThanOrEqual(2);
})->with([2, 3, 4, 5, 6]);
