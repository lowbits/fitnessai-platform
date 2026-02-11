<?php

use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Models\WorkoutTracking;
use App\Models\WorkoutTrackingExercise;
use App\Models\WorkoutTrackingSet;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->plan = Plan::factory()->create(['user_id' => $this->user->id]);
});

test('workout shows latest tracking by original_name across different workouts', function () {
    // Create first workout (Monday) with Bench Press
    $mondayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'workout_name' => 'Monday - Push Day',
    ]);
    $mondayBenchPress = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $mondayWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
        'sets' => 3,
        'reps' => 10,
    ]);

    // Create second workout (Thursday) with same exercise but different exercise_id
    $thursdayWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'workout_name' => 'Thursday - Push Day',
    ]);
    $thursdayBenchPress = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $thursdayWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press', // Same original_name, different ID
        'order' => 1,
        'sets' => 3,
        'reps' => 8,
    ]);

    // User completed Monday workout with tracking data
    $mondayTracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $mondayWorkout->id,
        'completed_at' => now()->subDays(3),
    ]);

    $mondayTrackingExercise = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $mondayTracking->id,
        'workout_plan_exercise_id' => $mondayBenchPress->id,
        'order' => 1,
        'notes' => 'Felt strong on Monday',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $mondayTrackingExercise->id,
        'set_number' => 1,
        'reps' => 10,
        'weight' => 80.0,
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $mondayTrackingExercise->id,
        'set_number' => 2,
        'reps' => 10,
        'weight' => 80.0,
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $mondayTrackingExercise->id,
        'set_number' => 3,
        'reps' => 9,
        'weight' => 80.0,
    ]);

    // Now user views Thursday workout - should see Monday's tracking data
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$thursdayWorkout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.id', $thursdayBenchPress->id)
        ->assertJsonPath('exercises.0.name', 'Bench Press')
        ->assertJsonPath('exercises.0.original_name', 'bench_press')
        ->assertJsonPath('exercises.0.latest_tracking.notes', 'Felt strong on Monday')
        ->assertJsonPath('exercises.0.latest_tracking.sets.0.set_number', 1)
        ->assertJsonPath('exercises.0.latest_tracking.sets.0.reps', 10)
        ->assertJsonPath('exercises.0.latest_tracking.sets.0.weight', 80)
        ->assertJsonPath('exercises.0.latest_tracking.sets.1.set_number', 2)
        ->assertJsonPath('exercises.0.latest_tracking.sets.1.reps', 10)
        ->assertJsonPath('exercises.0.latest_tracking.sets.1.weight', 80)
        ->assertJsonPath('exercises.0.latest_tracking.sets.2.set_number', 3)
        ->assertJsonPath('exercises.0.latest_tracking.sets.2.reps', 9)
        ->assertJsonPath('exercises.0.latest_tracking.sets.2.weight', 80);
});

test('workout shows most recent tracking when multiple trackings exist for same original_name', function () {
    // Create workout with Bench Press
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'workout_name' => 'Push Day',
    ]);
    $benchPress = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    // Create another workout with same exercise
    $workout2 = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'workout_name' => 'Another Push Day',
    ]);
    $benchPress2 = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout2->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    // Older tracking (week ago)
    $olderTracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $workout->id,
        'completed_at' => now()->subWeek(),
    ]);

    $olderTrackingExercise = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $olderTracking->id,
        'workout_plan_exercise_id' => $benchPress->id,
        'order' => 1,
        'notes' => 'Old workout',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $olderTrackingExercise->id,
        'set_number' => 1,
        'reps' => 8,
        'weight' => 70.0,
    ]);

    // Newer tracking (yesterday)
    $newerTracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $workout2->id,
        'completed_at' => now()->subDay(),
    ]);

    $newerTrackingExercise = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $newerTracking->id,
        'workout_plan_exercise_id' => $benchPress2->id,
        'order' => 1,
        'notes' => 'Recent workout',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $newerTrackingExercise->id,
        'set_number' => 1,
        'reps' => 10,
        'weight' => 85.0,
    ]);

    // Should show the newer tracking
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.latest_tracking.notes', 'Recent workout')
        ->assertJsonPath('exercises.0.latest_tracking.sets.0.reps', 10)
        ->assertJsonPath('exercises.0.latest_tracking.sets.0.weight', 85);
});

test('workout with multiple exercises shows correct latest tracking for each', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
        'workout_name' => 'Full Body',
    ]);

    $benchPress = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    $squat = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Squat',
        'original_name' => 'squat',
        'order' => 2,
    ]);

    // Create previous workout with tracking
    $previousWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);

    $previousBenchPress = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $previousWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
    ]);

    $previousSquat = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $previousWorkout->id,
        'name' => 'Squat',
        'original_name' => 'squat',
    ]);

    $tracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $previousWorkout->id,
        'completed_at' => now()->subDays(2),
    ]);

    // Bench Press tracking
    $benchPressTracking = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $tracking->id,
        'workout_plan_exercise_id' => $previousBenchPress->id,
        'order' => 1,
        'notes' => 'Bench notes',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $benchPressTracking->id,
        'set_number' => 1,
        'reps' => 10,
        'weight' => 80.0,
    ]);

    // Squat tracking
    $squatTracking = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $tracking->id,
        'workout_plan_exercise_id' => $previousSquat->id,
        'order' => 2,
        'notes' => 'Squat notes',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $squatTracking->id,
        'set_number' => 1,
        'reps' => 12,
        'weight' => 100.0,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.name', 'Bench Press')
        ->assertJsonPath('exercises.0.latest_tracking.notes', 'Bench notes')
        ->assertJsonPath('exercises.0.latest_tracking.sets.0.weight', 80)
        ->assertJsonPath('exercises.1.name', 'Squat')
        ->assertJsonPath('exercises.1.latest_tracking.notes', 'Squat notes')
        ->assertJsonPath('exercises.1.latest_tracking.sets.0.weight', 100);
});

test('exercise without original_name has no latest_tracking', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);

    $exerciseWithoutOriginalName = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Custom Exercise',
        'original_name' => null, // No original_name
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.name', 'Custom Exercise')
        ->assertJsonPath('exercises.0.original_name', null)
        ->assertJsonPath('exercises.0.latest_tracking', null);
});

test('exercise shows no latest_tracking when no previous tracking exists', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);

    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.name', 'Bench Press')
        ->assertJsonPath('exercises.0.latest_tracking', null);
});

test('user only sees their own tracking data, not other users', function () {
    $otherUser = User::factory()->create();
    $otherPlan = Plan::factory()->create(['user_id' => $otherUser->id]);

    // Create workout for current user
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);
    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    // Create workout for other user with same exercise
    $otherWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $otherPlan->id,
    ]);
    $otherExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $otherWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    // Other user completed workout
    $otherTracking = WorkoutTracking::factory()->create([
        'user_id' => $otherUser->id,
        'workout_plan_id' => $otherWorkout->id,
        'completed_at' => now()->subDay(),
    ]);

    $otherTrackingExercise = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $otherTracking->id,
        'workout_plan_exercise_id' => $otherExercise->id,
        'order' => 1,
        'notes' => 'Other user notes',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $otherTrackingExercise->id,
        'set_number' => 1,
        'reps' => 20,
        'weight' => 200.0,
    ]);

    // Current user should NOT see other user's tracking
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.latest_tracking', null);
});

test('only completed workouts are included in latest tracking', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);
    $exercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $workout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
        'order' => 1,
    ]);

    // Create previous workout
    $previousWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);
    $previousExercise = WorkoutPlanExercise::factory()->create([
        'workout_plan_id' => $previousWorkout->id,
        'name' => 'Bench Press',
        'original_name' => 'bench_press',
    ]);

    // Incomplete tracking (completed_at is null)
    $incompleteTracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $previousWorkout->id,
        'completed_at' => null, // NOT completed
    ]);

    $trackingExercise = WorkoutTrackingExercise::create([
        'workout_tracking_id' => $incompleteTracking->id,
        'workout_plan_exercise_id' => $previousExercise->id,
        'order' => 1,
        'notes' => 'Incomplete workout',
    ]);

    WorkoutTrackingSet::create([
        'workout_tracking_exercise_id' => $trackingExercise->id,
        'set_number' => 1,
        'reps' => 10,
        'weight' => 80.0,
    ]);

    // Should NOT show incomplete tracking
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $response->assertStatus(200)
        ->assertJsonPath('exercises.0.latest_tracking', null);
});

test('latest tracking query is performant with many exercises', function () {
    $workout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);

    // Create 10 exercises with same original_name pattern
    $exercises = [];
    for ($i = 1; $i <= 10; $i++) {
        $exercises[] = WorkoutPlanExercise::factory()->create([
            'workout_plan_id' => $workout->id,
            'name' => "Exercise {$i}",
            'original_name' => "exercise_{$i}",
            'order' => $i,
        ]);
    }

    // Create previous workouts with tracking data
    $previousWorkout = WorkoutPlan::factory()->create([
        'plan_id' => $this->plan->id,
    ]);

    $tracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $previousWorkout->id,
        'completed_at' => now()->subDay(),
    ]);

    foreach ($exercises as $index => $exercise) {
        $prevExercise = WorkoutPlanExercise::factory()->create([
            'workout_plan_id' => $previousWorkout->id,
            'name' => $exercise->name,
            'original_name' => $exercise->original_name,
        ]);

        $trackingExercise = WorkoutTrackingExercise::create([
            'workout_tracking_id' => $tracking->id,
            'workout_plan_exercise_id' => $prevExercise->id,
            'order' => $index + 1,
        ]);

        WorkoutTrackingSet::create([
            'workout_tracking_exercise_id' => $trackingExercise->id,
            'set_number' => 1,
            'reps' => 10,
            'weight' => 50.0 + $index,
        ]);
    }

    // Enable query logging to verify single query
    \DB::enableQueryLog();

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/workouts/{$workout->id}");

    $queries = \DB::getQueryLog();

    // Should be efficient - not N+1 queries
    // Typical queries: 1 for workout, 1 for exercises, 1 for trackings, 1-2 for relations
    expect(count($queries))->toBeLessThan(10);

    $response->assertStatus(200);

    // Verify all exercises have tracking
    for ($i = 0; $i < 10; $i++) {
        $response->assertJsonPath("exercises.{$i}.latest_tracking.sets.0.weight", 50 + $i);
    }
});
