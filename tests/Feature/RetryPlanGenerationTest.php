<?php

use App\Actions\RetryPlanGeneration;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('retries failed workout plans and dispatches job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 3, 'status' => 'generated']);

    RetryPlanGeneration::workouts($plan);

    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(2)
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'generated')->count())->toBe(1);

    Queue::assertPushed(GenerateUserWorkoutPlan::class, 1);
});

test('retries failed meal plans and dispatches job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);
    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'generated']);

    RetryPlanGeneration::meals($plan);

    expect(MealPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(1)
        ->and(MealPlan::where('plan_id', $plan->id)->where('status', 'generated')->count())->toBe(1);

    Queue::assertPushed(GenerateUserMealPlan::class, 1);
});

test('dispatches job for stuck pending workout plans without changing status', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'pending']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'pending']);

    RetryPlanGeneration::workouts($plan);

    // Pending plans should stay pending
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(2);

    Queue::assertPushed(GenerateUserWorkoutPlan::class, 1);
});

test('command retries pending plans with --status=pending', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'pending']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'generated']);

    $this->artisan('plans:retry-failed --type=workouts --status=pending')
        ->assertSuccessful();

    Queue::assertPushed(GenerateUserWorkoutPlan::class);
});

test('command retries both failed and pending with --status=failed,pending', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'pending']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 3, 'status' => 'generated']);

    $this->artisan('plans:retry-failed --type=workouts --status=failed,pending')
        ->assertSuccessful();

    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(2)
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'generated')->count())->toBe(1);

    Queue::assertPushed(GenerateUserWorkoutPlan::class);
});

test('command defaults to failed status only', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'pending']);

    $this->artisan('plans:retry-failed --type=workouts')
        ->assertSuccessful();

    Queue::assertNotPushed(GenerateUserWorkoutPlan::class);
});

test('command rejects invalid status', function () {
    $this->artisan('plans:retry-failed --status=invalid')
        ->assertFailed();
});
