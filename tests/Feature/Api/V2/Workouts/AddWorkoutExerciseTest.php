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

test('user can add an exercise to a workout', function () {
    $exercise = Exercise::factory()->create([
        'name' => 'Bench Press',
        'video_url' => 'https://example.com/bench.mp4',
        'image' => 'https://example.com/bench.jpg',
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
        ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'Exercise added successfully');

    $workoutExercise = WorkoutPlanExercise::where('workout_plan_id', $this->workout->id)->first();

    expect($workoutExercise->exercise_id)->toBe($exercise->id)
        ->and($workoutExercise->name)->toBe('Bench Press')
        ->and($workoutExercise->video_url)->toBe('https://example.com/bench.mp4')
        ->and($workoutExercise->image)->toBe('https://example.com/bench.jpg')
        ->and($workoutExercise->order)->toBe(1);
});

test('add appends exercise at the end of existing exercises', function () {
    WorkoutPlanExercise::factory()->count(2)->sequence(
        ['order' => 1],
        ['order' => 2],
    )->create(['workout_plan_id' => $this->workout->id]);

    $exercise = Exercise::factory()->create(['name' => 'New Exercise']);

    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
        ]);

    $response->assertCreated();

    $added = WorkoutPlanExercise::where('workout_plan_id', $this->workout->id)
        ->where('exercise_id', $exercise->id)
        ->first();

    expect($added->order)->toBe(3);
});

test('add requires authentication', function () {
    $response = $this->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
        'exercise_id' => 1,
    ]);

    $response->assertUnauthorized();
});

test('add is forbidden for another users workout', function () {
    $otherUser = User::factory()->create();
    $exercise = Exercise::factory()->create();

    $response = $this->actingAs($otherUser)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
        ]);

    $response->assertForbidden();
});

test('add requires exercise_id field', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_id');
});

test('add requires exercise_id to exist in exercises table', function () {
    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => 99999,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_id');
});

test('add returns 404 for non-existent workout', function () {
    $exercise = Exercise::factory()->create();

    $response = $this->actingAs($this->user)
        ->postJson('/api/v2/workouts/99999/exercises', [
            'exercise_id' => $exercise->id,
        ]);

    $response->assertNotFound();
});

test('add returns the created exercise in response', function () {
    $exercise = Exercise::factory()->create(['name' => 'Deadlift']);

    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
        ]);

    $response->assertCreated()
        ->assertJsonPath('exercise.name', 'Deadlift')
        ->assertJsonPath('exercise.exercise_id', $exercise->id)
        ->assertJsonPath('exercise.order', 1);
});

test('add accepts optional training parameters', function () {
    $exercise = Exercise::factory()->create(['name' => 'Squat']);

    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
            'sets' => 4,
            'reps' => 12,
            'duration_seconds' => 60,
            'tempo' => '3-1-2-0',
            'rpe' => '8',
        ]);

    $response->assertCreated();

    $workoutExercise = WorkoutPlanExercise::where('workout_plan_id', $this->workout->id)->first();

    expect($workoutExercise->sets)->toBe(4)
        ->and($workoutExercise->reps)->toBe(12)
        ->and($workoutExercise->duration_seconds)->toBe(60)
        ->and($workoutExercise->tempo)->toBe('3-1-2-0')
        ->and($workoutExercise->rpe)->toBe('8');
});

test('add works without training parameters', function () {
    $exercise = Exercise::factory()->create();

    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
        ]);

    $response->assertCreated();

    $workoutExercise = WorkoutPlanExercise::where('workout_plan_id', $this->workout->id)->first();

    expect($workoutExercise->sets)->toBeNull()
        ->and($workoutExercise->reps)->toBeNull()
        ->and($workoutExercise->duration_seconds)->toBeNull()
        ->and($workoutExercise->tempo)->toBeNull()
        ->and($workoutExercise->rpe)->toBeNull();
});

test('add validates training parameters types', function ($field, $value) {
    $exercise = Exercise::factory()->create();

    $response = $this->actingAs($this->user)
        ->postJson("/api/v2/workouts/{$this->workout->id}/exercises", [
            'exercise_id' => $exercise->id,
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
]);
