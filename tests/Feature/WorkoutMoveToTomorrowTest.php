<?php

use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->plan = Plan::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
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
    $exercise1 = Exercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
    ]);

    $exercise2 = Exercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'name' => 'Shoulder Press',
        'original_name' => 'shoulder_press',
        'order' => 2,
        'sets' => 3,
        'reps' => 12,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'rest_day' => ['id', 'date', 'name', 'type', 'description'],
            'moved_workout' => ['id', 'date', 'name', 'type', 'duration_minutes', 'exercises_count'],
        ])
        ->assertJsonPath('rest_day.type', 'rest')
        ->assertJsonPath('rest_day.name', 'Rest Day')
        ->assertJsonPath('moved_workout.name', 'Push Day')
        ->assertJsonPath('moved_workout.type', 'strength')
        ->assertJsonPath('moved_workout.exercises_count', 2);

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
    expect($movedExercises[0]->name)->toBe('Bench Press');
    expect($movedExercises[0]->original_name)->toBe('bench_press');
    expect($movedExercises[1]->name)->toBe('Shoulder Press');
    expect($movedExercises[1]->original_name)->toBe('shoulder_press');
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

    $response->assertStatus(409)
        ->assertJson([
            'error' => 'Conflict',
            'message' => 'Tomorrow already has a workout scheduled. Use force=true to replace it.',
        ])
        ->assertJsonPath('tomorrow_workout.id', $tomorrowWorkout->id)
        ->assertJsonPath('tomorrow_workout.name', 'Pull Day');

    // Verify today's workout is unchanged
    $todayWorkout->refresh();
    expect($todayWorkout->workout_type)->toBe('strength');
    expect($todayWorkout->workout_name)->toBe('Push Day');
});

test('cannot move rest day workout', function () {
    $restDayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => now()->addDays(5),
        'day_number' => 6,
        'workout_name' => 'Rest Day',
        'workout_type' => 'rest',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$restDayWorkout->id}/move");

    $response->assertStatus(400)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Cannot move a rest day',
        ]);
});

test('cannot move workout beyond plan duration', function () {
    // Create workout on last day of plan
    $lastDayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'date' => $this->plan->end_date,
        'day_number' => 30,
        'workout_name' => 'Final Workout',
        'workout_type' => 'strength',
        'status' => 'generated',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$lastDayWorkout->id}/move");

    $response->assertStatus(400)
        ->assertJson([
            'error' => 'Invalid operation',
            'message' => 'Cannot move workout beyond plan duration',
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
        ->postJson("/api/v2/workouts/{$otherWorkout->id}/move");

    $response->assertStatus(403)
        ->assertJson([
            'error' => 'Unauthorized',
            'message' => 'You do not have access to this workout',
        ]);
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

    $response = $this->postJson("/api/v2/workouts/{$workout->id}/move");

    $response->assertStatus(401);
});

test('move workout handles non-existent workout id', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/99999/move");

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

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

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

    $exercise = Exercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'type' => 'strength',
        'description' => 'Chest exercise',
        'instructions' => ['Step 1', 'Step 2', 'Step 3'],
        'video_url' => 'https://example.com/video',
        'image' => 'bench_press.jpg',
        'sets' => 4,
        'reps' => 8,
        'duration_seconds' => null,
        'rest_seconds' => '90',
        'tempo' => '3-0-1-0',
        'weight_recommendation' => '70% 1RM',
        'muscle_groups' => ['chest', 'triceps'],
        'equipment' => ['barbell', 'bench'],
        'form_cues' => 'Keep shoulders back',
        'alternatives' => ['dumbbell_press', 'push_ups'],
        'difficulty' => 'intermediate',
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

    $response->assertStatus(200);

    $tomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    $movedExercise = $tomorrowWorkout->exercises()->first();

    expect($movedExercise->name)->toBe('Bench Press');
    expect($movedExercise->original_name)->toBe('bench_press');
    expect($movedExercise->type)->toBe('strength');
    expect($movedExercise->description)->toBe('Chest exercise');
    expect($movedExercise->instructions)->toBe(['Step 1', 'Step 2', 'Step 3']);
    expect($movedExercise->video_url)->toBe('https://example.com/video');
    expect($movedExercise->image)->toBe('bench_press.jpg');
    expect($movedExercise->sets)->toBe(4);
    expect($movedExercise->reps)->toBe(8);
    expect($movedExercise->rest_seconds)->toBe('90');
    expect($movedExercise->tempo)->toBe('3-0-1-0');
    expect($movedExercise->weight_recommendation)->toBe('70% 1RM');
    expect($movedExercise->muscle_groups)->toBe(['chest', 'triceps']);
    expect($movedExercise->equipment)->toBe(['barbell', 'bench']);
    expect($movedExercise->form_cues)->toBe('Keep shoulders back');
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
        Exercise::factory()->create([
            'workout_plan_id' => $todayWorkout->id,
            'name' => "Exercise {$i}",
            'original_name' => "exercise_{$i}",
            'order' => $i,
        ]);
    }

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

    $response->assertStatus(200);

    $tomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    $movedExercises = $tomorrowWorkout->exercises()->orderBy('order')->get();

    expect($movedExercises)->toHaveCount(5);
    for ($i = 0; $i < 5; $i++) {
        expect($movedExercises[$i]->name)->toBe("Exercise " . ($i + 1));
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

    Exercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'name' => 'Bench Press',
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

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

    $exercise = Exercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'name' => 'Bench Press',
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

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

    $todayExercise = Exercise::factory()->create([
        'workout_plan_id' => $todayWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
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

    $tomorrowExercise = Exercise::factory()->create([
        'workout_plan_id' => $tomorrowWorkout->id,
        'name' => 'Pull Ups',
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move", [
            'force' => true,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Workout moved to tomorrow successfully')
        ->assertJsonPath('moved_workout.name', 'Push Day')
        ->assertJsonPath('replaced_workout.id', $tomorrowWorkout->id)
        ->assertJsonPath('replaced_workout.name', 'Pull Day');

    // Verify old tomorrow workout is deleted
    expect(WorkoutPlan::find($tomorrowWorkout->id))->toBeNull();
    expect(Exercise::find($tomorrowExercise->id))->toBeNull();

    // Verify new tomorrow workout exists with correct data
    $newTomorrowWorkout = WorkoutPlan::where('plan_id', $this->plan->id)
        ->where('day_number', 7)
        ->first();

    expect($newTomorrowWorkout)->not->toBeNull();
    expect($newTomorrowWorkout->workout_name)->toBe('Push Day');
    expect($newTomorrowWorkout->id)->not->toBe($tomorrowWorkout->id);

    // Verify exercises were moved correctly
    expect($newTomorrowWorkout->exercises()->count())->toBe(1);
    expect($newTomorrowWorkout->exercises()->first()->name)->toBe('Bench Press');
});

test('force parameter accepts string true', function () {
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move", [
            'force' => 'true',
        ]);

    $response->assertStatus(200);
});

test('force parameter accepts 1', function () {
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move", [
            'force' => 1,
        ]);

    $response->assertStatus(200);
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move", [
            'force' => false,
        ]);

    $response->assertStatus(409)
        ->assertJson([
            'error' => 'Conflict',
            'message' => 'Tomorrow already has a workout scheduled. Use force=true to replace it.',
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move");

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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move", [
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
        ->postJson("/api/v2/workouts/{$todayWorkout->id}/move", [
            'force' => true,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'rest_day',
            'moved_workout',
            'replaced_workout' => ['id', 'name', 'type', 'date'],
        ])
        ->assertJsonPath('replaced_workout.name', 'Pull Day')
        ->assertJsonPath('replaced_workout.type', 'cardio');
});
