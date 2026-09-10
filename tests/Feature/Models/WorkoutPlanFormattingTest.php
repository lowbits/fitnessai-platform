<?php

use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;

it('formats durations like the app', function () {
    expect((new WorkoutPlanExercise(['duration_seconds' => 30]))->durationLabel())->toBe('30s')
        ->and((new WorkoutPlanExercise(['duration_seconds' => 60]))->durationLabel())->toBe('1min')
        ->and((new WorkoutPlanExercise(['duration_seconds' => 90]))->durationLabel())->toBe('1min 30s')
        ->and((new WorkoutPlanExercise(['duration_seconds' => 0]))->durationLabel())->toBe('');
});

it('formats the sets metric by reps or time', function () {
    expect((new WorkoutPlanExercise(['sets' => 4, 'reps' => 8]))->metricLabel())->toBe('4 × 8')
        ->and((new WorkoutPlanExercise(['sets' => 3, 'duration_seconds' => 30]))->metricLabel())->toBe('3 × 30s');
});

it('lists up to two alternative names', function () {
    $exercise = new WorkoutPlanExercise([
        'alternatives' => [['name' => 'Incline Press'], ['name' => 'Dumbbell Press'], ['name' => 'Machine Press']],
    ]);

    expect($exercise->alternativeNames())->toBe('Incline Press, Dumbbell Press');
});

it('translates muscle group labels for the current locale', function () {
    app()->setLocale('de');

    $plan = new WorkoutPlan(['muscle_groups' => ['chest', 'shoulders', 'cardio']]);

    expect($plan->muscleGroupLabels())->toBe(['Brust', 'Schultern', 'Cardio']);
});
