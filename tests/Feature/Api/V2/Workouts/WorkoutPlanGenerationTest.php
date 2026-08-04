<?php

use App\Ai\Agents\WorkoutProgrammerAgent;
use App\Jobs\GenerateMealPlanBatch;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('workout generation does not generate a day locked by a concurrent worker', function () {
    WorkoutProgrammerAgent::fake(fn () => 'Fake workout response');

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->startOfDay(),
        'end_date' => now()->addDays(7)->startOfDay(),
        'duration_days' => 7,
        'workouts_per_week' => 7,
    ]);

    $held = Cache::lock("workout_gen:{$plan->id}:1", 10);
    expect($held->get())->toBeTrue();

    $job = new GenerateUserWorkoutPlan($user, $plan, maxDays: 1, lockWaitSeconds: 1);
    $job->handle();

    expect(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 1)->exists())->toBeFalse();

    $held->release();
});

test('complete plan exits early without creating new records', function () {
    WorkoutProgrammerAgent::fake(fn () => 'Fake workout response');

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->startOfDay(),
        'duration_days' => 7,
        'workouts_per_week' => 4,
    ]);

    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $job->handle();

    expect(WorkoutPlan::where('plan_id', $plan->id)->count())->toBe(7)
        ->and(WorkoutPlan::where('plan_id', $plan->id)->where('day_number', 8)->exists())->toBeFalse();
});

test('meal plan retry dispatches batches when middle day failed', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->startOfDay(),
        'duration_days' => 7,
    ]);

    // Day 2 failed, all others generated
    for ($day = 1; $day <= 7; $day++) {
        MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => $day === 2 ? 'failed' : 'generated',
        ]);
    }

    // Reset failed → pending (like the retry command does)
    MealPlan::where('plan_id', $plan->id)->where('status', 'failed')->update(['status' => 'pending']);

    $job = new GenerateUserMealPlan($user, $plan);
    $job->handle();

    Queue::assertPushed(GenerateMealPlanBatch::class);
});

test('meal plan skips generation when all days are generated', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->startOfDay(),
        'duration_days' => 7,
    ]);

    for ($day = 1; $day <= 7; $day++) {
        MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserMealPlan($user, $plan);
    $job->handle();

    Queue::assertNotPushed(GenerateMealPlanBatch::class);
});

test('meal plan past days are skipped', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();

    // Plan started 10 days ago, 3 days already expired without generation
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->subDays(10)->startOfDay(),
        'duration_days' => 30,
    ]);

    // Days 1-10 already generated (past)
    for ($day = 1; $day <= 10; $day++) {
        MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserMealPlan($user, $plan);
    $job->handle();

    // Should dispatch batches for days from today (day 11) onwards
    Queue::assertPushed(GenerateMealPlanBatch::class);
});

test('workout generation calculate day range starts from today', function () {
    WorkoutProgrammerAgent::fake(fn () => 'Fake workout response');

    $user = User::factory()->withProfile()->create();

    // Plan started 10 days ago
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->subDays(10)->startOfDay(),
        'end_date' => now()->addDays(20)->startOfDay(),
        'duration_days' => 30,
        'workouts_per_week' => 7,
    ]);

    // Use reflection to test calculateDayRange directly
    $job = new GenerateUserWorkoutPlan($user, $plan);
    $method = new ReflectionMethod($job, 'calculateDayRange');

    [$startDay, $endDay] = $method->invoke($job);

    // Today is day 11 (10 days after start + 1), should start from there
    expect($startDay)->toBe(11)
        ->and($endDay)->toBe(17); // 11 + 6 = 17 (7 days at a time)
});

test('workout generation skips past days in calculate range', function () {
    WorkoutProgrammerAgent::fake(fn () => 'Fake workout response');

    $user = User::factory()->withProfile()->create();

    // Plan started 5 days ago, days 1-5 generated, day 6 (today) not yet
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->subDays(5)->startOfDay(),
        'end_date' => now()->addDays(25)->startOfDay(),
        'duration_days' => 30,
        'workouts_per_week' => 7,
    ]);

    for ($day = 1; $day <= 5; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $method = new ReflectionMethod($job, 'calculateDayRange');

    [$startDay, $endDay] = $method->invoke($job);

    // Today is day 6, should start from there
    expect($startDay)->toBe(6)
        ->and($endDay)->toBe(12); // 6 + 6 = 12
});

test('workout generation returns null when all future days generated', function () {
    WorkoutProgrammerAgent::fake(fn () => 'Fake workout response');

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'start_date' => now()->startOfDay(),
        'end_date' => now()->addDays(7)->startOfDay(),
        'duration_days' => 7,
        'workouts_per_week' => 7,
    ]);

    for ($day = 1; $day <= 7; $day++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $day,
            'status' => 'generated',
        ]);
    }

    $job = new GenerateUserWorkoutPlan($user, $plan);
    $method = new ReflectionMethod($job, 'calculateDayRange');

    [$startDay, $endDay] = $method->invoke($job);

    expect($startDay)->toBeNull()
        ->and($endDay)->toBeNull();
});
