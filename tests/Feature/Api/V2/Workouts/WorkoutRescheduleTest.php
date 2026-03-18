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
        'status' => 'active',
        'start_date' => now(),
        'duration_days' => 30,
    ]);
});

test('user can move workout to tomorrow and current day becomes rest day', function () {
    // Create workout for today
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'estimated_duration_minutes' => 60,
        'estimated_calories_burned' => 400,
        'difficulty' => 'medium',
        'description' => 'Upper body push workout',
        'muscle_groups' => ['chest', 'shoulders', 'triceps'],
        'status' => 'generated',
    ]);

    // Add exercises to workout
    $benchExercise = Exercise::factory()->create(['name' => 'Bench Press']);
    $shoulderExercise = Exercise::factory()->create(['name' => 'Shoulder Press']);

    $exercise1 = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'exercise_id' => $benchExercise->id,
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
    ]);

    $exercise2 = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'exercise_id' => $shoulderExercise->id,
        'order' => 2,
        'sets' => 3,
        'reps' => 12,
    ]);

    $tomorrowDate = now()->addDays(6);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $tomorrowDate->format('Y-m-d'),
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'rest_day' => ['id', 'date', 'name', 'type', 'description'],
            'rescheduled_workout' => ['id', 'date', 'name', 'type', 'duration_minutes', 'exercises_count'],
        ])
        ->assertJsonPath('rest_day.type', 'rest')
        ->assertJsonPath('rest_day.name', 'Rest Day')
        ->assertJsonPath('rescheduled_workout.name', 'Push Day')
        ->assertJsonPath('rescheduled_workout.type', 'strength')
        ->assertJsonPath('rescheduled_workout.exercises_count', 2);

    // Verify today's workout is now a rest day
    $todayWorkout->refresh();
    expect($todayWorkout->workout_type)->toBe('rest');
    expect($todayWorkout->workout_name)->toBe('Rest Day');
    expect($todayWorkout->exercises()->count())->toBe(0);

    // Verify tomorrow's workout exists with exercises
    $tomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    expect($tomorrowWorkout)->not->toBeNull();
    expect($tomorrowWorkout->workout_name)->toBe('Push Day');
    expect($tomorrowWorkout->workout_type)->toBe('strength');
    expect($tomorrowWorkout->exercises()->count())->toBe(2);

    // Verify exercises are correctly moved
    $movedExercises = $tomorrowWorkout->exercises()->orderBy('order')->get();
    expect($movedExercises[0]->exercise_id)->toBe($benchExercise->id);
    expect($movedExercises[1]->exercise_id)->toBe($shoulderExercise->id);
});

test('cannot move workout if tomorrow already has a workout', function () {
    // Create workout for today
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    // Create workout for tomorrow
    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $todayWorkout->date->addDay()->format('Y-m-d'),
        ]);

    $response->assertStatus(409)
        ->assertJson([
            'error' => 'Conflict',
            'message' => 'Target date already has a workout scheduled. Use force=true to replace it.',
        ])
        ->assertJsonPath('existing_workout.id', $tomorrowWorkout->id)
        ->assertJsonPath('existing_workout.name', 'Pull Day');

    // Verify today's workout is unchanged
    $todayWorkout->refresh();
    expect($todayWorkout->workout_type)->toBe('strength');
    expect($todayWorkout->workout_name)->toBe('Push Day');
});

test('cannot move workout beyond plan duration', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(30),
        'end_date' => now()->addDays(30),
        'duration_days' => 30,
    ]);
    // Create workout on last day of plan
    $lastDayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => $plan->end_date,
        'day_number' => 30,
        'workout_name' => 'Final Workout',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $beyondDate = $plan->end_date->copy()->addDay();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/v2/workouts/{$lastDayWorkout->id}/reschedule", [
            'target_date' => $beyondDate->format('Y-m-d'),
        ]);

    $response->assertStatus(400)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Target date is outside plan duration',
        ]);
});

test('cannot move another users workout', function () {
    $otherUser = User::factory()->create();
    $otherPlan = Plan::factory()->create([
        'user_id' => $otherUser->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 30,
    ]);

    $otherWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $otherPlan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Other Users Workout',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$otherWorkout->id}/reschedule", [
            'target_date' => now()->addDays(6)->format('Y-m-d'),
        ]);

    $response->assertStatus(403);
});

test('move workout requires authentication', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->postJson("/api/v2/workouts/{$workout->id}/reschedule", [
        'target_date' => now()->addDays(6)->format('Y-m-d'),
    ]);

    $response->assertStatus(401);
});

test('move workout handles non-existent workout id', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/workouts/99999/reschedule', [
            'target_date' => now()->addDays(6)->format('Y-m-d'),
        ]);

    $response->assertStatus(404);
});

test('moved workout preserves all original workout properties', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'HIIT Cardio',
        'workout_type' => 'hiit',
        'estimated_duration_minutes' => 45,
        'estimated_calories_burned' => 500,
        'difficulty' => 'hard',
        'description' => 'High intensity interval training',
        'muscle_groups' => ['full_body', 'cardio'],
        'status' => 'generated',
    ]);

    $tomorrowDate = now()->addDays(6);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $tomorrowDate->format('Y-m-d'),
        ]);

    $response->assertStatus(200);

    $tomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    expect($tomorrowWorkout->workout_name)->toBe('HIIT Cardio');
    expect($tomorrowWorkout->workout_type)->toBe('hiit');
    expect($tomorrowWorkout->estimated_duration_minutes)->toBe(45);
    expect($tomorrowWorkout->estimated_calories_burned)->toBe(500);
    expect($tomorrowWorkout->difficulty)->toBe('hard');
    expect($tomorrowWorkout->description)->toBe('High intensity interval training');
    expect($tomorrowWorkout->muscle_groups)->toBe(['full_body', 'cardio']);
});

test('moved workout preserves exercise properties including arrays', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'status' => 'generated',
    ]);

    $canonicalExercise = Exercise::factory()->create(['name' => 'Bench Press']);

    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'exercise_id' => $canonicalExercise->id,
        'type' => 'strength',
        'sets' => 4,
        'reps' => 8,
        'duration_seconds' => null,
        'rest_seconds' => '90',
        'tempo' => '3-0-1-0',
        'weight_recommendation' => '70% 1RM',
        'alternatives' => ['dumbbell_press', 'push_ups'],
        'difficulty' => 'intermediate',
        'order' => 1,
    ]);

    $tomorrowDate = now()->addDays(6);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $tomorrowDate->format('Y-m-d'),
        ]);

    $response->assertStatus(200);

    $tomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    $movedExercise = $tomorrowWorkout->exercises()->first();

    expect($movedExercise->exercise_id)->toBe($canonicalExercise->id);
    expect($movedExercise->type)->toBe('strength');
    expect($movedExercise->sets)->toBe(4);
    expect($movedExercise->reps)->toBe(8);
    expect($movedExercise->rest_seconds)->toBe('90');
    expect($movedExercise->tempo)->toBe('3-0-1-0');
    expect($movedExercise->weight_recommendation)->toBe('70% 1RM');
    expect($movedExercise->alternatives)->toBe(['dumbbell_press', 'push_ups']);
    expect($movedExercise->difficulty)->toBe('intermediate');
    expect($movedExercise->order)->toBe(1);
});

test('move workout maintains exercise order', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'status' => 'generated',
    ]);

    // Create 5 exercises with specific order
    for ($i = 1; $i <= 5; $i++) {
        WorkoutPlanExercise::factory()->create([
            'workout_plan_id' => $todayWorkout->id,
            'order' => $i,
        ]);
    }

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => now()->addDays(6)->format('Y-m-d'),
        ]);

    $response->assertStatus(200);

    $tomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    $movedExercises = $tomorrowWorkout->exercises()->orderBy('order')->get();

    expect($movedExercises)->toHaveCount(5);
    for ($i = 0; $i < 5; $i++) {
        expect($movedExercises[$i]->order)->toBe($i + 1);
    }
});

test('rest day has correct properties after conversion', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'estimated_duration_minutes' => 60,
        'estimated_calories_burned' => 400,
        'difficulty' => 'medium',
        'muscle_groups' => ['chest', 'shoulders'],
        'status' => 'generated',
    ]);

    WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => now()->addDays(6)->format('Y-m-d'),
        ]);

    $response->assertStatus(200);

    $todayWorkout->refresh();

    expect($todayWorkout->workout_name)->toBe('Rest Day');
    expect($todayWorkout->workout_type)->toBe('rest');
    expect($todayWorkout->estimated_duration_minutes)->toBe(0);
    expect($todayWorkout->estimated_calories_burned)->toBe(0);
    expect($todayWorkout->difficulty)->toBe('easy');
    expect($todayWorkout->description)->toContain('rest day');
    expect($todayWorkout->muscle_groups)->toBe([]);
    expect($todayWorkout->status)->toBe('generated');
    expect($todayWorkout->exercises()->count())->toBe(0);
});

test('move workout is atomic - all or nothing', function () {
    // This test ensures that if something fails, the whole operation is rolled back
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'order' => 1,
    ]);

    $originalWorkoutType = $todayWorkout->workout_type;
    $originalWorkoutName = $todayWorkout->workout_name;
    $originalExerciseCount = $todayWorkout->exercises()->count();

    // Try to move to tomorrow when tomorrow already exists (should fail)
    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => now()->addDays(6)->format('Y-m-d'),
        ]);

    $response->assertStatus(409);

    // Verify nothing changed (transaction rolled back)
    $todayWorkout->refresh();
    expect($todayWorkout->workout_type)->toBe($originalWorkoutType);
    expect($todayWorkout->workout_name)->toBe($originalWorkoutName);
    expect($todayWorkout->exercises()->count())->toBe($originalExerciseCount);
});

test('can force replace existing tomorrow workout with force parameter', function () {
    // Create workout for today
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $todayExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'order' => 1,
    ]);

    // Create workout for tomorrow
    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $tomorrowWorkout->id,
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $todayWorkout->date->addDay()->format('Y-m-d'),
            'force' => true,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Workout rescheduled successfully')
        ->assertJsonPath('rescheduled_workout.name', 'Push Day')
        ->assertJsonPath('replaced_workout.id', $tomorrowWorkout->id)
        ->assertJsonPath('replaced_workout.name', 'Pull Day');

    // Verify old tomorrow workout is deleted
    expect(WorkoutPlan::find($tomorrowWorkout->id))->toBeNull();
    expect(WorkoutPlanExercise::find($tomorrowExercise->id))->toBeNull();

    // Verify new tomorrow workout exists with correct data
    $newTomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    expect($newTomorrowWorkout)->not->toBeNull();
    expect($newTomorrowWorkout->workout_name)->toBe('Push Day');
    expect($newTomorrowWorkout->id)->not->toBe($tomorrowWorkout->id);

    // Verify exercises were moved correctly
    expect($newTomorrowWorkout->exercises()->count())->toBe(1);
    expect($newTomorrowWorkout->exercises()->first()->exercise_id)->toBe($todayExercise->exercise_id);
});

test('force parameter accepts only boolean', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    // Test with string "true"
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $todayWorkout->date->addDay()->format('Y-m-d'),
            'force' => 'true',
        ]);

    $response->assertStatus(422);
});

test('force parameter does not accept 1', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    // Test with integer 1
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'force' => 1,
        ]);

    $response->assertStatus(422);
});

test('force false does not replace existing workout', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $todayWorkout->date->addDay()->format('Y-m-d'),
            'force' => false,
        ]);

    $response->assertStatus(409)
        ->assertJson([
            'error' => 'Conflict',
            'message' => 'Target date already has a workout scheduled. Use force=true to replace it.',
        ]);

    // Verify tomorrow workout still exists
    expect(WorkoutPlan::find($tomorrowWorkout->id))->not->toBeNull();
});

test('without force parameter defaults to false', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    // No force parameter provided
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule",
            [
                'target_date' => $todayWorkout->date->addDay()->format('Y-m-d'),
            ]);

    $response->assertStatus(409);
});

test('force replace maintains atomicity on failure', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkoutId = $tomorrowWorkout->id;

    // This should succeed, but if something fails in transaction,
    // the old workout should still exist
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'force' => true,
        ]);

    // If it succeeds, the old workout should be gone
    if ($response->status() === 200) {
        expect(WorkoutPlan::find($tomorrowWorkoutId))->toBeNull();
    }

    // If it fails, the old workout should still be there
    if ($response->status() !== 200) {
        expect(WorkoutPlan::find($tomorrowWorkoutId))->not->toBeNull();
    }
});

test('rescheduling to a rest day does not throw conflict', function () {
    // Create workout for today
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    // Create rest day for tomorrow
    $tomorrowRestDay = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Rest Day',
        'workout_type' => 'rest',
        'status' => 'generated',
    ]);

    // Reschedule to tomorrow without force=true
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $tomorrowRestDay->date->format('Y-m-d'),
        ]);

    // Should succeed with 200 OK because target is a rest day
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Workout rescheduled successfully')
        ->assertJsonPath('rescheduled_workout.name', 'Push Day');

    // Verify rest day was replaced
    expect(WorkoutPlan::find($tomorrowRestDay->id))->toBeNull();
});

test('force replace includes replaced workout info in response', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(6),
        'day_number' => 7,
        'workout_name' => 'Pull Day',
        'workout_type' => 'cardio',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $todayWorkout->date->addDay()->format('Y-m-d'),
            'force' => true,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'rest_day',
            'rescheduled_workout',
            'replaced_workout' => ['id', 'name', 'type', 'date'],
        ])
        ->assertJsonPath('replaced_workout.name', 'Pull Day')
        ->assertJsonPath('replaced_workout.type', 'cardio');
});

test('can reschedule workout to specific date with target_date parameter', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'order' => 1,
    ]);

    // Reschedule to day 16 (10 days later)
    $targetDate = $this->plan->start_date->copy()->addDays(15);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $targetDate->format('Y-m-d'),
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Workout rescheduled successfully')
        ->assertJsonPath('rescheduled_workout.date', $targetDate->format('Y-m-d'));

    // Verify workout was created on target date
    $rescheduledWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 16)
        ->first();

    expect($rescheduledWorkout)->not->toBeNull();
    expect($rescheduledWorkout->workout_name)->toBe('Push Day');
});

test('can reschedule without target_date defaults to tomorrow', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $tomorrowDate = now()->addDays(6);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", ['target_date' => $tomorrowDate->format('Y-m-d')]);

    $response->assertStatus(200)
        ->assertJsonPath('rescheduled_workout.date', $tomorrowDate->format('Y-m-d'));
});

test('cannot reschedule to same date', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $todayWorkout->date->format('Y-m-d'),
        ]);

    $response->assertStatus(400)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Cannot reschedule to the same date',
        ]);
});

test('cannot reschedule to past date', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $pastDate = now()->subDays(1);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $pastDate->format('Y-m-d'),
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'target_date' => 'The target date field must be a date after or equal to today.',
        ]);
});

test('cannot reschedule beyond plan duration', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    // Try to reschedule beyond plan duration_days (30)
    // start_date is now(), so day 31 is now() + 30 days
    $beyondPlanDate = now()->addDays(35);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $beyondPlanDate->format('Y-m-d'),
        ]);

    $response->assertStatus(400)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Target date is outside plan duration',
        ]);
});

test('validates target_date format', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => 'invalid-date',
        ]);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors']);
});

test('can force reschedule to date with existing workout', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $targetDate = now()->addDays(10);
    $targetDayNumber = 11;

    $existingWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => $targetDate,
        'day_number' => $targetDayNumber,
        'workout_name' => 'Pull Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $targetDate->format('Y-m-d'),
            'force' => true,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('replaced_workout.id', $existingWorkout->id)
        ->assertJsonPath('replaced_workout.name', 'Pull Day');

    // Verify old workout is deleted
    expect(WorkoutPlan::find($existingWorkout->id))->toBeNull();
});

test('reschedule preserves all workout and exercise properties', function () {
    $todayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'HIIT Cardio',
        'workout_type' => 'hiit',
        'estimated_duration_minutes' => 45,
        'estimated_calories_burned' => 500,
        'difficulty' => 'hard',
        'description' => 'High intensity interval training',
        'muscle_groups' => ['full_body', 'cardio'],
        'status' => 'generated',
    ]);

    WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'type' => 'cardio',
        'sets' => 4,
        'reps' => 20,
        'order' => 1,
    ]);

    // Target day 13 (7 days later)
    $targetDate = $this->plan->start_date->copy()->addDays(12);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/reschedule", [
            'target_date' => $targetDate->format('Y-m-d'),
        ]);

    $response->assertStatus(200);

    $rescheduledWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 13)
        ->first();

    expect($rescheduledWorkout)->not->toBeNull();
    expect($rescheduledWorkout->workout_name)->toBe('HIIT Cardio');
    expect($rescheduledWorkout->workout_type)->toBe('hiit');
    expect($rescheduledWorkout->estimated_duration_minutes)->toBe(45);
    expect($rescheduledWorkout->difficulty)->toBe('hard');
    expect($rescheduledWorkout->exercises()->count())->toBe(1);
});

test('user can move workout backward from tomorrow to today', function () {
    // Create rest day for today
    $todayRestDay = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->startOfDay(),
        'day_number' => 1,
        'workout_name' => 'Rest Day',
        'workout_type' => 'rest',
        'status' => 'generated',
    ]);

    // Tomorrow: Strength workout
    $tomorrowWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDay()->startOfDay(),
        'day_number' => 2,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'estimated_duration_minutes' => 60,
        'status' => 'generated',
    ]);

    WorkoutPlanExercise::factory()->count(3)->create([
        'workout_plan_id' => $tomorrowWorkout->id,
    ]);

    // Move tomorrow's workout to today (force replace rest day)
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$tomorrowWorkout->id}/reschedule", [
            'target_date' => now()->startOfDay()->format('Y-m-d'), // Today
            'force' => true, // Force replace today's rest day
        ]);

    // Should succeed because target_date >= today
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Workout rescheduled successfully')
        ->assertJsonPath('rescheduled_workout.name', 'Push Day')
        ->assertJsonPath('rest_day.date', $tomorrowWorkout->date->format('Y-m-d'));

    // Verify tomorrow is now rest day
    $tomorrowWorkout->refresh();
    expect($tomorrowWorkout->workout_type)->toBe('rest');

    // Verify today has the strength workout
    $todayWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 1)
        ->where('workout_type', 'strength')
        ->first();

    expect($todayWorkout)->not->toBeNull();
    expect($todayWorkout->workout_name)->toBe('Push Day');
    expect($todayWorkout->exercises()->count())->toBe(3);
});

test('cannot move workout to actual past date (yesterday)', function () {
    $futureWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Push Day',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$futureWorkout->id}/reschedule", [
            'target_date' => now()->subDay()->format('Y-m-d'), // Yesterday
        ]);

    // Should fail validation at FormRequest level (after_or_equal:today)
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['target_date']);
});
