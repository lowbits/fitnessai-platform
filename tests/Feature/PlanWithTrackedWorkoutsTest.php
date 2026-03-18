<?php

use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTracking;
use App\Models\WorkoutTrackingExercise;
use App\Models\WorkoutTrackingSet;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can see tracked workouts when fetching day plan', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    $workoutPlan = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
    ]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
    ]);

    $tracking = WorkoutTracking::factory()->completed()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $workoutPlan->id,
        'feeling_rate' => 4,
        'notes' => 'Great session',
    ]);

    $exercise = WorkoutTrackingExercise::factory()->create([
        'workout_tracking_id' => $tracking->id,
        'order' => 1,
    ]);

    WorkoutTrackingSet::factory()->create([
        'workout_tracking_exercise_id' => $exercise->id,
        'set_number' => 1,
        'reps' => 10,
        'weight' => 60.0,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$date}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'tracked_workouts' => [
                'entries' => [
                    '*' => [
                        'id',
                        'workout_plan_id',
                        'started_at',
                        'completed_at',
                        'is_completed',
                        'feeling_rate',
                        'notes',
                        'exercises' => [
                            '*' => [
                                'id',
                                'workout_plan_exercise_id',
                                'order',
                                'sets' => [
                                    '*' => [
                                        'id',
                                        'set_number',
                                        'reps',
                                        'weight',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'count',
            ],
        ])
        ->assertJson([
            'tracked_workouts' => [
                'count' => 1,
                'entries' => [
                    [
                        'is_completed' => true,
                        'feeling_rate' => 4,
                        'notes' => 'Great session',
                    ],
                ],
            ],
        ]);

    $entry = $response->json('tracked_workouts.entries.0');
    expect($entry['exercises'])->toHaveCount(1);
    expect($entry['exercises'][0]['sets'])->toHaveCount(1);
    expect($entry['exercises'][0]['sets'][0]['reps'])->toBe(10);
    expect((float) $entry['exercises'][0]['sets'][0]['weight'])->toBe(60.0);

    // Workout itself should be marked as completed
    expect($response->json('workout.is_completed'))->toBeTrue();
});

test('day plan shows empty tracked workouts when nothing is tracked', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
    ]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$date}");

    $response->assertStatus(200)
        ->assertJson([
            'tracked_workouts' => [
                'entries' => [],
                'count' => 0,
            ],
        ]);

    // Workout should not be marked as completed
    expect($response->json('workout.is_completed'))->toBeFalse();
});

test('tracked workouts only include tracking for the requested day workout plan', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $today = now()->startOfDay();
    $dayNumber = $plan->start_date->startOfDay()->diffInDays($today) + 1;

    $todayWorkoutPlan = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $today->format('Y-m-d'),
    ]);

    $yesterdayWorkoutPlan = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber - 1,
        'date' => $today->subDay()->format('Y-m-d'),
    ]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
    ]);

    // Track today's workout
    WorkoutTracking::factory()->completed()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $todayWorkoutPlan->id,
    ]);

    // Track yesterday's workout
    WorkoutTracking::factory()->completed()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $yesterdayWorkoutPlan->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertStatus(200)
        ->assertJson([
            'tracked_workouts' => [
                'count' => 1,
            ],
        ]);
});
