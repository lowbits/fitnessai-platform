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

// ─── Reorder Tests ──────────────────────────────────────────────────────────────

test('user can reorder workout exercises', function () {
    $exercises = WorkoutPlanExercise::factory()->count(3)->sequence(
        ['order' => 1],
        ['order' => 2],
        ['order' => 3],
    )->create(['workout_plan_id' => $this->workout->id]);

    $newOrder = [$exercises[2]->id, $exercises[0]->id, $exercises[1]->id];

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => $newOrder,
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('message', 'Exercises reordered successfully');

    expect($exercises[2]->fresh()->order)->toBe(1);
    expect($exercises[0]->fresh()->order)->toBe(2);
    expect($exercises[1]->fresh()->order)->toBe(3);
});

test('reorder requires authentication', function () {
    $response = $this->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
        'exercise_ids' => [1, 2, 3],
    ]);

    $response->assertUnauthorized();
});

test('reorder is forbidden for another users workout', function () {
    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => [1],
        ]);

    $response->assertForbidden();
});

test('reorder requires exercise_ids field', function () {
    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_ids');
});

test('reorder requires exercise_ids to be an array', function () {
    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => 'not-an-array',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_ids');
});

test('reorder requires exercise_ids to contain integers', function () {
    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => ['abc', 'def'],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_ids.0');
});

test('reorder requires exercise_ids to be distinct', function () {
    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => [$exercise->id, $exercise->id],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_ids.1');
});

test('reorder fails if exercise ids do not belong to workout', function () {
    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $otherWorkout = WorkoutPlan::factory()->create(['plan_id' => $this->plan->id]);
    $otherExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $otherWorkout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => [$exercise->id, $otherExercise->id],
        ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Exercise IDs must match the workout\'s exercises exactly.');
});

test('reorder fails if not all exercise ids are provided', function () {
    $exercises = WorkoutPlanExercise::factory()->count(3)->sequence(
        ['order' => 1],
        ['order' => 2],
        ['order' => 3],
    )->create(['workout_plan_id' => $this->workout->id]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => [$exercises[0]->id, $exercises[1]->id],
        ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Exercise IDs must match the workout\'s exercises exactly.');
});

test('reorder works with a single exercise', function () {
    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/reorder", [
            'exercise_ids' => [$exercise->id],
        ]);

    $response->assertSuccessful();
    expect($exercise->fresh()->order)->toBe(1);
});

test('reorder returns 404 for non-existent workout', function () {
    $response = $this->actingAs($this->user)
        ->putJson('/api/v2/workouts/99999/exercises/reorder', [
            'exercise_ids' => [1],
        ]);

    $response->assertNotFound();
});

// ─── Replace Tests ──────────────────────────────────────────────────────────────

test('user can replace a workout exercise', function () {
    $oldExercise = Exercise::factory()->create(['name' => 'Bench Press']);
    $newExercise = Exercise::factory()->create([
        'name' => 'Dumbbell Press',
        'description' => 'A dumbbell press variation',
    ]);

    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'exercise_id' => $oldExercise->id,
        'order' => 1,
        'sets' => 4,
        'reps' => 10,
        'rest_seconds' => '60',
        'tempo' => '2-1-2-0',
        'rpe' => 8,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}/replace", [
            'exercise_id' => $newExercise->id,
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('message', 'Exercise replaced successfully')
        ->assertJsonPath('exercise.exercise_id', $newExercise->id)
        ->assertJsonPath('exercise.name', 'Dumbbell Press')
        ->assertJsonPath('exercise.sets', 4)
        ->assertJsonPath('exercise.reps', 10)
        ->assertJsonStructure([
            'exercise' => ['id', 'exercise_id', 'name', 'type', 'sets', 'reps', 'muscle_groups', 'equipment'],
        ]);

    $workoutExercise->refresh();

    // exercise_id should be updated
    expect($workoutExercise->exercise_id)->toBe($newExercise->id);

    // Training params should be preserved
    expect($workoutExercise->order)->toBe(1);
    expect($workoutExercise->sets)->toBe(4);
    expect($workoutExercise->reps)->toBe(10);
    expect($workoutExercise->rest_seconds)->toBe('60');
    expect($workoutExercise->tempo)->toBe('2-1-2-0');
});

test('replace requires authentication', function () {
    $response = $this->putJson("/api/v2/workouts/{$this->workout->id}/exercises/1/replace", [
        'exercise_id' => 1,
    ]);

    $response->assertUnauthorized();
});

test('replace is forbidden for another users workout', function () {
    $otherUser = User::factory()->create();

    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($otherUser)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}/replace", [
            'exercise_id' => 1,
        ]);

    $response->assertForbidden();
});

test('replace requires exercise_id field', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}/replace", []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_id');
});

test('replace requires exercise_id to exist in exercises table', function () {
    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}/replace", [
            'exercise_id' => 99999,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('exercise_id');
});

test('replace uses canonical exercise data in response', function () {
    $newExercise = Exercise::factory()->create([
        'name' => 'Cable Fly',
        'video_url' => 'https://example.com/cable-fly.mp4',
        'image' => 'https://example.com/cable-fly.jpg',
    ]);

    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}/replace", [
            'exercise_id' => $newExercise->id,
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('exercise.name', 'Cable Fly')
        ->assertJsonPath('exercise.video_url', 'https://example.com/cable-fly.mp4')
        ->assertJsonPath('exercise.image', 'https://example.com/cable-fly.jpg');

    $workoutExercise->refresh();
    expect($workoutExercise->exercise_id)->toBe($newExercise->id);
});

test('replace returns 404 for exercise not belonging to workout', function () {
    $otherWorkout = WorkoutPlan::factory()->create(['plan_id' => $this->plan->id]);
    $otherExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $otherWorkout->id,
        'order' => 1,
    ]);

    $newExercise = Exercise::factory()->create();

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/{$otherExercise->id}/replace", [
            'exercise_id' => $newExercise->id,
        ]);

    $response->assertNotFound();
});

test('replace returns 404 for non-existent workout', function () {
    $response = $this->actingAs($this->user)
        ->putJson('/api/v2/workouts/99999/exercises/1/replace', [
            'exercise_id' => 1,
        ]);

    $response->assertNotFound();
});

test('replace returns 404 for non-existent exercise id in url', function () {
    $newExercise = Exercise::factory()->create();

    $response = $this->actingAs($this->user)
        ->putJson("/api/v2/workouts/{$this->workout->id}/exercises/99999/replace", [
            'exercise_id' => $newExercise->id,
        ]);

    $response->assertNotFound();
});

// ─── Remove Tests ───────────────────────────────────────────────────────────────

test('user can remove a workout exercise', function () {
    $exercises = WorkoutPlanExercise::factory()->count(3)->sequence(
        ['order' => 1],
        ['order' => 2],
        ['order' => 3],
    )->create(['workout_plan_id' => $this->workout->id]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/{$exercises[1]->id}");

    $response->assertSuccessful()
        ->assertJsonPath('message', 'Exercise removed successfully');

    // Exercise B should be deleted
    expect(WorkoutPlanExercise::find($exercises[1]->id))->toBeNull();

    // Remaining exercises should be re-ordered
    expect($exercises[0]->fresh()->order)->toBe(1);
    expect($exercises[2]->fresh()->order)->toBe(2);
});

test('remove requires authentication', function () {
    $response = $this->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/1");

    $response->assertUnauthorized();
});

test('remove is forbidden for another users workout', function () {
    $otherUser = User::factory()->create();

    $workoutExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($otherUser)
        ->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/{$workoutExercise->id}");

    $response->assertForbidden();
});

test('remove re-orders remaining exercises correctly', function () {
    $exercises = WorkoutPlanExercise::factory()->count(4)->sequence(
        ['order' => 1],
        ['order' => 2],
        ['order' => 3],
        ['order' => 4],
    )->create(['workout_plan_id' => $this->workout->id]);

    // Remove the first exercise
    $this->actingAs($this->user)
        ->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/{$exercises[0]->id}");

    expect($exercises[1]->fresh()->order)->toBe(1);
    expect($exercises[2]->fresh()->order)->toBe(2);
    expect($exercises[3]->fresh()->order)->toBe(3);
});

test('remove returns 404 for exercise not belonging to workout', function () {
    $otherWorkout = WorkoutPlan::factory()->create(['plan_id' => $this->plan->id]);
    $otherExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $otherWorkout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/{$otherExercise->id}");

    $response->assertNotFound();
});

test('remove the last exercise from a workout', function () {
    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $this->workout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/{$exercise->id}");

    $response->assertSuccessful();
    expect(WorkoutPlanExercise::where('workout_plan_id', $this->workout->id)->count())->toBe(0);
});

test('remove returns 404 for non-existent workout', function () {
    $response = $this->actingAs($this->user)
        ->deleteJson('/api/v2/workouts/99999/exercises/1');

    $response->assertNotFound();
});

test('remove returns 404 for non-existent exercise id', function () {
    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v2/workouts/{$this->workout->id}/exercises/99999");

    $response->assertNotFound();
});
