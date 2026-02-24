<?php

use App\Actions\RevenueCat\AdjustActivePlanAction;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

test('monthly subscription adds 1 month on top of remaining days', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(4),
        'end_date' => now()->addDays(3), // 3 days remaining
        'duration_days' => 7,
    ]);

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'premium_monthly');

    $plan->refresh();

    // End date should be current end_date + 1 month (not start_date + 1 month)
    $expectedEndDate = now()->addDays(3)->addMonthNoOverflow();
    expect($plan->end_date->toDateString())->toBe($expectedEndDate->toDateString());
    expect($plan->duration_days)->toBeGreaterThan(30); // 7 original + ~30 = ~37
});

test('annual subscription adds 1 year on top of remaining days', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(4),
        'end_date' => now()->addDays(10), // 10 days remaining
        'duration_days' => 14,
    ]);

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'premium_annual');

    $plan->refresh();

    $expectedEndDate = now()->addDays(10)->addYearNoOverflow();
    expect($plan->end_date->toDateString())->toBe($expectedEndDate->toDateString());
    expect($plan->duration_days)->toBeGreaterThan(365);
});

test('yearly product id is also detected as annual', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(2),
        'end_date' => now()->addDays(5),
        'duration_days' => 7,
    ]);

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'premium_yearly');

    $plan->refresh();

    $expectedEndDate = now()->addDays(5)->addYearNoOverflow();
    expect($plan->end_date->toDateString())->toBe($expectedEndDate->toDateString());
});

test('expired plan adds subscription from today', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(14),
        'end_date' => now()->subDays(7), // expired 7 days ago
        'duration_days' => 7,
    ]);

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'premium_monthly');

    $plan->refresh();

    // Should add 1 month from today (not from the expired end_date)
    $expectedEndDate = now()->startOfDay()->addMonthNoOverflow();
    expect($plan->end_date->toDateString())->toBe($expectedEndDate->toDateString());
});

test('custom months parameter works', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(3),
        'end_date' => now()->addDays(4),
        'duration_days' => 7,
    ]);

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'custom_product', months: 3);

    $plan->refresh();

    $expectedEndDate = now()->addDays(4)->addMonthsNoOverflow(3);
    expect($plan->end_date->toDateString())->toBe($expectedEndDate->toDateString());
});

test('no active plan logs warning and does nothing', function () {
    $user = User::factory()->create();

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'premium_monthly');

    expect(Plan::where('user_id', $user->id)->count())->toBe(0);
});

test('triggers generation when user is behind on workout plans', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(4),
        'duration_days' => 14,
    ]);

    // Last generated workout is only 1 day from now (running out)
    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 11,
        'date' => now()->addDay(),
        'status' => 'generated',
    ]);

    $action = new AdjustActivePlanAction;
    $action->execute($user, 'premium_monthly');

    Queue::assertPushed(\App\Jobs\GenerateUserWorkoutPlan::class);
    Queue::assertPushed(\App\Jobs\GenerateUserMealPlan::class);
});
