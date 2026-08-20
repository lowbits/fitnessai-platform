<?php

use App\Ai\Tools\GetTodayWorkoutTool;
use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Laravel\Ai\Tools\Request;

function workoutFixture(string $type = 'strength', string $status = 'generated'): array
{
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today()]);
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => today(),
        'status' => $status,
        'workout_type' => $type,
    ]);

    if ($type !== 'rest' && $status === 'generated') {
        $exercise = Exercise::factory()->create();
        WorkoutPlanExercise::factory()->for($workout, 'workoutPlan')->create([
            'exercise_id' => $exercise->id,
            'order' => 1,
        ]);
    }

    return [$user, $plan, $workout];
}

function todayWorkout(User $user): array
{
    return json_decode((new GetTodayWorkoutTool($user))->handle(new Request([])), true);
}

test('with no active plan it returns no_active_plan', function () {
    $result = todayWorkout(User::factory()->withProfile()->create());

    expect($result['error'])->toBe('no_active_plan');
});

test('a generated training day returns a workout_today widget', function () {
    [$user] = workoutFixture('strength');

    $result = todayWorkout($user);

    expect($result['widget'])->toBe('workout_today');
    expect($result['data']['is_rest_day'])->toBeFalse();
    expect($result['data']['exercises_count'])->toBe(1);
});

test('a rest day reports is_rest_day with the next workout', function () {
    [$user, $plan] = workoutFixture('rest');
    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => today()->addDay(),
        'status' => 'generated',
        'workout_type' => 'strength',
        'workout_name' => 'Push Day',
    ]);

    $result = todayWorkout($user);

    expect($result['data']['is_rest_day'])->toBeTrue();
    expect($result['data']['next_workout']['name'])->toBe('Push Day');
});

test('a not-yet-generated workout returns workout_not_generated', function () {
    [$user] = workoutFixture('strength', 'pending');

    expect(todayWorkout($user)['error'])->toBe('workout_not_generated');
});

test('no workout row for today returns workout_not_generated', function () {
    $user = User::factory()->withProfile()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today()]);

    expect(todayWorkout($user)['error'])->toBe('workout_not_generated');
});
