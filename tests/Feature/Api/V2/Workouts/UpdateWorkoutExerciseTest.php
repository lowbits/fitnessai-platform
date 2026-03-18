<?php

use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->plan = Plan::factory()->create([
        'user_id' => $this->user->id,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'duration_days' => 30,
    ]);

    $this->workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'workout_type' => 'strength',
    ]);
});

test('user can update a workout exercise', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
        'duration_seconds' => null,
        'tempo' => '2-0-2-0',
        'rpe' => '7',
        'rest_seconds' => '60',
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            'sets' => 5,
            'reps' => 8,
            'duration_seconds' => 45,
            'tempo' => '3-1-2-0',
            'rpe' => '9',
            'rest_seconds' => '90',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('message', 'Exercise updated successfully');

    $workoutExercise->refresh();

    expect($workoutExercise->sets)->toBe(5);
    expect($workoutExercise->reps)->toBe(8);
    expect($workoutExercise->duration_seconds)->toBe(45);
    expect($workoutExercise->tempo)->toBe('3-1-2-0');
    expect($workoutExercise->rpe)->toBe('9');
    expect($workoutExercise->rest_seconds)->toBe('90');
});

test('update allows partial updates', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
        'tempo' => '2-0-2-0',
        'rpe' => '7',
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            'sets' => 5,
        ]);

    $response->assertSuccessful();

    $workoutExercise->refresh();

    expect($workoutExercise->sets)->toBe(5);
    expect($workoutExercise->reps)->toBe(10);
    expect($workoutExercise->tempo)->toBe('2-0-2-0');
    expect($workoutExercise->rpe)->toBe('7');
});

test('update does not change order or exercise identity', function () {
    $exercise = Exercise::factory()->create(['name' => 'Bench Press']);

    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'exercise_id' => $exercise->id,
        'order' => 2,
        'sets' => 3,
    ]);

    $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            'sets' => 5,
        ]);

    $workoutExercise->refresh();

    expect($workoutExercise->exercise_id)->toBe($exercise->id);
    expect($workoutExercise->order)->toBe(2);
});

test('update allows clearing a field by setting it to null', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
        'tempo' => '2-0-2-0',
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            'tempo' => null,
            'reps' => null,
        ]);

    $response->assertSuccessful();

    $workoutExercise->refresh();

    expect($workoutExercise->tempo)->toBeNull();
    expect($workoutExercise->reps)->toBeNull();
    expect($workoutExercise->sets)->toBe(3);
});

test('update requires authentication', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
        'sets' => 5,
    ]);

    $response->assertUnauthorized();
});

test('update is forbidden for another users workout', function () {
    $otherUser = User::factory()->create();

    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($otherUser)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            'sets' => 5,
        ]);

    $response->assertForbidden();
});

test('update validates training parameter types', function ($field, $value) {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            $field => $value,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'sets must be integer' => ['sets', 'not-a-number'],
    'sets must be at least 1' => ['sets', 0],
    'reps must be integer' => ['reps', 'not-a-number'],
    'reps must be at least 1' => ['reps', 0],
    'duration_seconds must be integer' => ['duration_seconds', 'not-a-number'],
    'duration_seconds must be at least 1' => ['duration_seconds', 0],
    'tempo must be string' => ['tempo', 123],
    'rpe must be string' => ['rpe', true],
    'rest_seconds must be string' => ['rest_seconds', 123],
]);

test('update returns 404 for exercise not belonging to workout', function () {
    $otherWorkout = WorkoutPlan::factory()->create(['plan_id' => $this->plan->id]);
    $otherExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $otherWorkout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$otherExercise->id}", [
            'sets' => 5,
        ]);

    $response->assertNotFound();
});

test('update returns 404 for non-existent workout', function () {
    $response = $this->actingAs($this->user)
        ->putJson('/api/v2/workouts/99999/exercises/1', [
            'sets' => 5,
        ]);

    $response->assertNotFound();
});

test('update returns 404 for non-existent exercise id', function () {
    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/99999", [
            'sets' => 5,
        ]);

    $response->assertNotFound();
});

test('update returns the updated exercise in response', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}", [
            'sets' => 5,
            'reps' => 8,
            'tempo' => '3-1-2-0',
            'rpe' => '9',
            'rest_seconds' => '45',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('exercise.id', $workoutExercise->id)
        ->assertJsonPath('exercise.sets', 5)
        ->assertJsonPath('exercise.reps', 8)
        ->assertJsonPath('exercise.tempo', '3-1-2-0')
        ->assertJsonPath('exercise.rpe', '9')
        ->assertJsonPath('exercise.rest_seconds', '45');
});
