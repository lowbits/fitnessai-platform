<?php

use App\Actions\Workouts\PopulateWorkoutPlanAction;
use App\Ai\DataTransferObjects\WorkoutPlanResult;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutPlan;

function workoutPlanForGoal(string $state): WorkoutPlan
{
    $user = User::factory()->create();
    UserProfile::factory()->for($user)->{$state}()->create();
    $plan = Plan::factory()->for($user)->create();

    return WorkoutPlan::factory()->for($plan)->create();
}

function resultWith(array $exercises): WorkoutPlanResult
{
    return new WorkoutPlanResult(
        workoutName: 'Full Body A',
        workoutType: 'strength',
        description: null,
        estimatedDurationMinutes: 40,
        difficulty: 'intermediate',
        muscleGroups: ['chest'],
        exercises: $exercises,
    );
}

function strengthExercise(array $overrides = []): array
{
    return array_merge([
        'exercise_id' => null,
        'type' => 'strength',
        'sets' => 3,
        'reps' => 10,
    ], $overrides);
}

it('backfills a goal-based rpe when the model leaves it empty', function () {
    $workoutPlan = workoutPlanForGoal('buildMuscle');

    (new PopulateWorkoutPlanAction)->execute($workoutPlan, resultWith([strengthExercise()]));

    expect($workoutPlan->exercises()->first()->rpe)->toBe('7-9');
});

it('uses the moderate default for non-hypertrophy goals', function () {
    $workoutPlan = workoutPlanForGoal('loseWeight');

    (new PopulateWorkoutPlanAction)->execute($workoutPlan, resultWith([strengthExercise()]));

    expect($workoutPlan->exercises()->first()->rpe)->toBe('6-8');
});

it('normalizes a messy rpe the model returned', function () {
    $workoutPlan = workoutPlanForGoal('buildMuscle');

    (new PopulateWorkoutPlanAction)->execute($workoutPlan, resultWith([
        strengthExercise(['rpe' => 'RPE 8/10']),
    ]));

    expect($workoutPlan->exercises()->first()->rpe)->toBe('8');
});

it('leaves warmups without an rpe', function () {
    $workoutPlan = workoutPlanForGoal('buildMuscle');

    (new PopulateWorkoutPlanAction)->execute($workoutPlan, resultWith([
        strengthExercise(['type' => 'warmup', 'rpe' => null]),
    ]));

    expect($workoutPlan->exercises()->first()->rpe)->toBeNull();
});
