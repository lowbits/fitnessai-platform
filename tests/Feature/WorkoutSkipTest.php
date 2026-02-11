<?php

use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'locale' => 'en',
    ]);

    $this->plan = Plan::factory()->create([
        'user_id' => $this->user->id,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'duration_days' => 30,
        'workouts_per_week' => 3,
    ]);
});

test('user can skip a workout', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'description' => 'Focus on chest, shoulders, and triceps',
        'estimated_duration_minutes' => 45,
        'estimated_calories_burned' => 300,
        'difficulty' => 'Intermediate',
        'muscle_groups' => ['chest', 'shoulders', 'triceps'],
    ]);

    // Add some exercises
    WorkoutPlanExercise::factory()->count(3)->create([
        'workout_plan_id' => $workout->id,
    ]);

    $response = $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $workout));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Workout skipped successfully',
            'original_workout' => [
                'id' => $workout->id,
                'name' => 'Push Day',
                'type' => 'strength',
                'exercises_count' => 3,
            ],
        ])
        ->assertJsonPath('rest_day.type', 'rest')
        ->assertJsonPath('rest_day.date', $workout->date->format('Y-m-d'));

    // Verify original workout was soft deleted
    expect($workout->fresh()->trashed())->toBeTrue();

    // Verify a new rest day was created
    $restDay = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('date', $workout->date)
        ->where('workout_type', 'rest')
        ->whereNull('deleted_at')
        ->first();

    expect($restDay)->not->toBeNull();
    expect($restDay->id)->not->toBe($workout->id);
    expect($restDay->day_number)->toBe($workout->day_number);
});

test('user cannot skip a rest day', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Rest Day',
        'workout_type' => 'rest',
    ]);

    $response = $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $workout));

    $response->assertStatus(422)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Cannot skip a rest day',
        ]);
});

test('user cannot skip an already skipped workout', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    // Skip the workout first (soft delete it)
    $workout->delete();

    $response = $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $workout));

    $response->assertStatus(404);
});

test('user cannot skip another users workout', function () {
    $otherUser = User::factory()->create();
    $otherPlan = Plan::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $otherPlan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    $response = $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $workout));

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'This action is unauthorized.',
        ]);
});

test('guest cannot skip a workout', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    $response = $this->postJson(route('workouts.skip', $workout));

    $response->assertStatus(401);
});

test('skipped workout exercises remain in database', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    WorkoutPlanExercise::factory()->count(3)->create([
        'workout_plan_id' => $workout->id,
    ]);

    expect($workout->exercises()->count())->toBe(3);

    $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $workout));

    // Exercises should still exist in database
    expect($workout->exercises()->count())->toBe(3);

    // But workout is soft deleted
    expect($workout->fresh()->trashed())->toBeTrue();
});

test('cannot skip workout if active workout with same day_number exists', function () {
    // Create first workout
    $workout1 = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
    ]);

    // Manually create another active workout with same day_number (data inconsistency scenario)
    $workout2 = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1, // Same day_number!
        'status' => 'generated',
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
    ]);

    $response = $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $workout1));

    $response->assertStatus(409)
        ->assertJson([
            'error' => 'Conflict',
            'message' => 'An active workout already exists for this day',
        ]);

    // Neither workout should be deleted
    expect($workout1->fresh()->trashed())->toBeFalse();
    expect($workout2->fresh()->trashed())->toBeFalse();
});

test('rest day remains when skipped', function () {
    $restDay = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay(),
        'day_number' => 1,
        'status' => 'generated',
        'workout_name' => 'Rest Day',
        'workout_type' => 'rest',
    ]);

    $response = $this->actingAs($this->user)
        ->postJson(route('workouts.skip', $restDay));

    $response->assertStatus(422);

    // Rest day should still be active
    expect($restDay->fresh()->trashed())->toBeFalse();
});
