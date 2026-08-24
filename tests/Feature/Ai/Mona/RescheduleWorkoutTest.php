<?php

use App\Actions\RescheduleWorkout;
use App\Ai\Tools\RescheduleWorkoutTool;
use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Laravel\Ai\Tools\Request;

function trainingToday(): array
{
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => today(),
        'duration_days' => 30,
    ]);
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => today(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_type' => 'strength',
        'workout_name' => 'Push Day',
    ]);
    $exercise = Exercise::factory()->create();
    WorkoutPlanExercise::factory()->for($workout, 'workoutPlan')->create(['exercise_id' => $exercise->id, 'order' => 1]);

    return [$user, $plan, $workout];
}

function reschedule(User $user, array $args): array
{
    $tool = new RescheduleWorkoutTool($user, new RescheduleWorkout);

    return json_decode($tool->handle(new Request($args)), true);
}

test('a rest day has nothing to move', function () {
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today()]);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => today(), 'workout_type' => 'rest', 'status' => 'generated']);

    expect(reschedule($user, ['action' => 'skip'])['error'])->toBe('no_workout_today');
});

test('skipping rests today and drops the session', function () {
    [$user, , $workout] = trainingToday();

    $result = reschedule($user, ['action' => 'skip']);

    expect($result['widget'])->toBe('workout_rescheduled');
    expect($result['data']['outcome'])->toBe('skipped');
    expect(WorkoutPlan::whereDate('date', today())->where('workout_type', 'rest')->exists())->toBeTrue();
    expect(WorkoutPlan::withTrashed()->find($workout->id)->trashed())->toBeTrue();
});

test('moving copies the workout to the target day and rests today', function () {
    [$user, , $workout] = trainingToday();

    $result = reschedule($user, ['action' => 'move', 'target_date' => today()->addDay()->format('Y-m-d')]);

    expect($result['data']['outcome'])->toBe('moved');
    expect($workout->fresh()->workout_type)->toBe('rest');
    expect(WorkoutPlan::whereDate('date', today()->addDay())->where('workout_name', 'Push Day')->exists())->toBeTrue();
});

test('moving onto an existing workout needs confirmation, then replaces it', function () {
    [$user, $plan, $workout] = trainingToday();
    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => today()->addDay(),
        'day_number' => 2,
        'status' => 'generated',
        'workout_type' => 'strength',
        'workout_name' => 'Leg Day',
    ]);

    $conflict = reschedule($user, ['action' => 'move', 'target_date' => today()->addDay()->format('Y-m-d')]);
    expect($conflict['error'])->toBe('target_conflict');
    expect($conflict['conflict']['name'])->toBe('Leg Day');

    $confirmed = reschedule($user, [
        'action' => 'move',
        'target_date' => today()->addDay()->format('Y-m-d'),
        'confirmed' => true,
    ]);
    expect($confirmed['data']['outcome'])->toBe('moved');
    expect(WorkoutPlan::whereDate('date', today()->addDay())->where('workout_name', 'Push Day')->exists())->toBeTrue();
});

test('move without a target day asks for one', function () {
    [$user] = trainingToday();

    expect(reschedule($user, ['action' => 'move'])['error'])->toBe('need_target_date');
});
