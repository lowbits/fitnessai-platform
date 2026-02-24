<?php

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('command retries failed workout plans', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 3, 'status' => 'generated']);

    $this->artisan('plans:retry-failed --type=workouts')
        ->assertSuccessful();

    // Failed workouts should be reset to pending
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(2);
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'generated')->count())->toBe(1);

    Queue::assertPushed(GenerateUserWorkoutPlan::class);
});

test('command retries failed nutrition plans', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'generated']);
    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'failed']);
    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 3, 'status' => 'generated']);

    $this->artisan('plans:retry-failed --type=nutrition')
        ->assertSuccessful();

    expect(MealPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(1);
    expect(MealPlan::where('plan_id', $plan->id)->where('status', 'generated')->count())->toBe(2);

    Queue::assertPushed(GenerateUserMealPlan::class);
});

test('command retries both workouts and nutrition when no type specified', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);
    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);

    $this->artisan('plans:retry-failed')
        ->assertSuccessful();

    Queue::assertPushed(GenerateUserWorkoutPlan::class);
    Queue::assertPushed(GenerateUserMealPlan::class);
});

test('command can filter by plan id', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan1 = Plan::factory()->create(['user_id' => $user->id]);
    $plan2 = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan1->id, 'day_number' => 1, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan2->id, 'day_number' => 1, 'status' => 'failed']);

    $this->artisan("plans:retry-failed --plan={$plan1->id}")
        ->assertSuccessful();

    // Only plan1 should be reset, plan2 stays failed
    expect(WorkoutPlan::where('plan_id', $plan1->id)->where('status', 'pending')->count())->toBe(1);
    expect(WorkoutPlan::where('plan_id', $plan2->id)->where('status', 'failed')->count())->toBe(1);
});

test('command can filter by user id', function () {
    Queue::fake();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $plan1 = Plan::factory()->create(['user_id' => $user1->id]);
    $plan2 = Plan::factory()->create(['user_id' => $user2->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan1->id, 'day_number' => 1, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan2->id, 'day_number' => 1, 'status' => 'failed']);

    $this->artisan("plans:retry-failed --user={$user1->id}")
        ->assertSuccessful();

    expect(WorkoutPlan::where('plan_id', $plan1->id)->where('status', 'pending')->count())->toBe(1);
    expect(WorkoutPlan::where('plan_id', $plan2->id)->where('status', 'failed')->count())->toBe(1);
});

test('command can reset status without dispatching jobs', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);
    MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'failed']);

    $this->artisan('plans:retry-failed --reset')
        ->assertSuccessful();

    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(1);
    expect(MealPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(1);

    Queue::assertNotPushed(GenerateUserWorkoutPlan::class);
    Queue::assertNotPushed(GenerateUserMealPlan::class);
});

test('command handles no failed plans gracefully', function () {
    $this->artisan('plans:retry-failed')
        ->assertSuccessful();
});

test('command rejects invalid type option', function () {
    $this->artisan('plans:retry-failed --type=invalid')
        ->assertFailed();
});
