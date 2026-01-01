<?php

use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTracking;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $this->user->id]);
    $this->workoutPlan = WorkoutPlan::factory()->create(['plan_id' => $plan->id]);
    $this->exercise1 = Exercise::factory()->create(['workout_plan_id' => $this->workoutPlan->id, 'order' => 1]);
    $this->exercise2 = Exercise::factory()->create(['workout_plan_id' => $this->workoutPlan->id, 'order' => 2]);
});

test('user can start tracking a workout', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/workouts', [
            'workout_plan_id' => $this->workoutPlan->id,
            'started_at' => now()->toISOString(),
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'workout_plan_id',
                'started_at',
                'completed_at',
                'notes',
                'feeling_rate',
                'exercises',
            ],
        ]);

    $this->assertDatabaseHas('workout_trackings', [
        'user_id' => $this->user->id,
        'workout_plan_id' => $this->workoutPlan->id,
    ]);
});

test('user can complete a workout tracking with exercises', function () {
    $startedAt = now()->subHour();
    $completedAt = now();

    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/workouts', [
            'workout_plan_id' => $this->workoutPlan->id,
            'started_at' => $startedAt->toISOString(),
            'completed_at' => $completedAt->toISOString(),
            'notes' => 'Great workout!',
            'feeling_rate' => 5,
            'exercises' => [
                [
                    'exercise_id' => $this->exercise1->id,
                    'order' => 1,
                    'notes' => 'Felt strong',
                    'sets' => [
                        [
                            'set_number' => 1,
                            'reps' => 12,
                            'weight' => 50.5,
                            'rpe' => 7,
                        ],
                        [
                            'set_number' => 2,
                            'reps' => 10,
                            'weight' => 55.0,
                            'rpe' => 8,
                        ],
                        [
                            'set_number' => 3,
                            'reps' => 8,
                            'weight' => 57.5,
                            'rpe' => 9,
                        ],
                    ],
                ],
                [
                    'exercise_id' => $this->exercise2->id,
                    'order' => 2,
                    'notes' => 'Good cardio',
                    'sets' => [
                        [
                            'set_number' => 1,
                            'duration' => 300,
                        ],
                    ],
                ],
            ],
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'workout_plan_id' => $this->workoutPlan->id,
                'notes' => 'Great workout!',
                'feeling_rate' => 5,
            ],
        ]);

    $this->assertDatabaseHas('workout_trackings', [
        'user_id' => $this->user->id,
        'workout_plan_id' => $this->workoutPlan->id,
        'notes' => 'Great workout!',
        'feeling_rate' => 5,
    ]);

    $this->assertDatabaseHas('workout_tracking_exercises', [
        'exercise_id' => $this->exercise1->id,
        'notes' => 'Felt strong',
    ]);

    $this->assertDatabaseHas('workout_tracking_sets', [
        'set_number' => 1,
        'reps' => 12,
        'weight' => 50.5,
        'rpe' => 7,
    ]);

    $this->assertDatabaseHas('workout_tracking_sets', [
        'set_number' => 2,
        'reps' => 10,
        'weight' => 55.0,
        'rpe' => 8,
    ]);

    $this->assertDatabaseHas('workout_tracking_sets', [
        'set_number' => 3,
        'reps' => 8,
        'weight' => 57.5,
        'rpe' => 9,
    ]);
});

test('user can get their workout trackings', function () {
    WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $this->workoutPlan->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/workouts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'workout_plan_id',
                    'started_at',
                    'completed_at',
                    'notes',
                    'feeling_rate',
                    'exercises',
                ],
            ],
        ]);
});

test('user can get a specific workout tracking', function () {
    $tracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $this->workoutPlan->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/track/workouts/{$tracking->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $tracking->id,
                'workout_plan_id' => $this->workoutPlan->id,
            ],
        ]);
});

test('user can update a workout tracking', function () {
    $tracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $this->workoutPlan->id,
        'completed_at' => null,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/v2/track/workouts/{$tracking->id}", [
            'completed_at' => now()->toISOString(),
            'notes' => 'Updated notes',
            'feeling_rate' => 4,
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('workout_trackings', [
        'id' => $tracking->id,
        'notes' => 'Updated notes',
        'feeling_rate' => 4,
    ]);
});

test('user cannot access another users workout tracking', function () {
    $otherUser = User::factory()->create();
    $tracking = WorkoutTracking::factory()->create([
        'user_id' => $otherUser->id,
        'workout_plan_id' => $this->workoutPlan->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/track/workouts/{$tracking->id}");

    $response->assertStatus(403);
});

test('workout tracking requires authentication', function () {
    $response = $this->postJson('/api/v2/track/workouts', [
        'workout_plan_id' => $this->workoutPlan->id,
        'started_at' => now()->toISOString(),
    ]);

    $response->assertStatus(401);
});

test('workout tracking requires valid workout_plan_id', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/workouts', [
            'workout_plan_id' => 99999,
            'started_at' => now()->toISOString(),
        ]);

    $response->assertStatus(422);
});

test('feeling_rate must be between 1 and 5', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/workouts', [
            'workout_plan_id' => $this->workoutPlan->id,
            'started_at' => now()->toISOString(),
            'feeling_rate' => 6,
        ]);

    $response->assertStatus(422);
});

test('user can delete a workout tracking', function () {
    $tracking = WorkoutTracking::factory()->create([
        'user_id' => $this->user->id,
        'workout_plan_id' => $this->workoutPlan->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/v2/track/workouts/{$tracking->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('workout_trackings', [
        'id' => $tracking->id,
    ]);
});

