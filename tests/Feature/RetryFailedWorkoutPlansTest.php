<?php

use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('command can retry failed workout plans', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    // Create some failed workouts
    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'failed',
    ]);

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 2,
        'status' => 'failed',
    ]);

    // Create a successful workout (should not be affected)
    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 3,
        'status' => 'generated',
    ]);

    $this->artisan('workouts:retry-failed')
        ->expectsConfirmation('Do you want to retry generating these workout plans?', 'yes')
        ->assertSuccessful();

    // Check that failed workouts were reset to pending
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(2);
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'failed')->count())->toBe(0);

    // Check that job was dispatched
    Queue::assertPushed(GenerateUserWorkoutPlan::class);
});

test('command can filter by plan id', function () {
    Queue::fake();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $plan1 = Plan::factory()->create(['user_id' => $user1->id]);
    $plan2 = Plan::factory()->create(['user_id' => $user2->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan1->id, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan2->id, 'status' => 'failed']);

    $this->artisan("workouts:retry-failed --plan={$plan1->id}")
        ->expectsConfirmation('Do you want to retry generating these workout plans?', 'yes')
        ->assertSuccessful();

    // Only plan1's workout should be reset
    expect(WorkoutPlan::where('plan_id', $plan1->id)->where('status', 'pending')->count())->toBe(1);
    expect(WorkoutPlan::where('plan_id', $plan2->id)->where('status', 'failed')->count())->toBe(1);
});

test('command can filter by user id', function () {
    Queue::fake();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $plan1 = Plan::factory()->create(['user_id' => $user1->id]);
    $plan2 = Plan::factory()->create(['user_id' => $user2->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan1->id, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan2->id, 'status' => 'failed']);

    $this->artisan("workouts:retry-failed --user={$user1->id}")
        ->expectsConfirmation('Do you want to retry generating these workout plans?', 'yes')
        ->assertSuccessful();

    // Only user1's workouts should be reset
    expect(WorkoutPlan::where('plan_id', $plan1->id)->where('status', 'pending')->count())->toBe(1);
    expect(WorkoutPlan::where('plan_id', $plan2->id)->where('status', 'failed')->count())->toBe(1);
});

test('command can reset status without dispatching job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'status' => 'failed']);

    $this->artisan('workouts:retry-failed --reset')
        ->assertSuccessful();

    // Status should be reset but no job dispatched
    expect(WorkoutPlan::where('plan_id', $plan->id)->where('status', 'pending')->count())->toBe(1);
    Queue::assertNotPushed(GenerateUserWorkoutPlan::class);
});

test('command handles no failed workouts gracefully', function () {
    $this->artisan('workouts:retry-failed')
        ->expectsOutput('No failed workout plans found.')
        ->assertSuccessful();
});

test('command can be cancelled by user', function () {
    Queue::fake();

    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'status' => 'failed']);

    $this->artisan('workouts:retry-failed')
        ->expectsConfirmation('Do you want to retry generating these workout plans?', 'no')
        ->expectsOutput('Operation cancelled.')
        ->assertSuccessful();

    // Nothing should change
    expect(WorkoutPlan::where('status', 'failed')->count())->toBe(1);
    Queue::assertNotPushed(GenerateUserWorkoutPlan::class);
});

