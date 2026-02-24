<?php

use App\Models\Plan;
use App\Models\User;

test('can create beta subscription for user', function () {
    $user = User::factory()->create();

    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->fresh()->hasActiveLegacySubscription())->toBeTrue();
});

test('subscription extends existing plan by adding month on top of remaining days', function () {
    // Create user with 7-day plan
    $user = User::factory()->create();

    $startDate = now()->startOfDay();
    $endDate = $startDate->copy()->addDays(7);

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    expect($plan->duration_days)->toBe(7);

    // Create subscription
    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    // Subscription adds 1 month on top of remaining end_date
    $updatedPlan = $plan->fresh();
    $expectedEndDate = $endDate->copy()->addMonthNoOverflow();
    $expectedDuration = (int) $startDate->diffInDays($expectedEndDate);
    expect($updatedPlan->duration_days)->toBe($expectedDuration);
    expect((int) $updatedPlan->start_date->diffInDays($updatedPlan->end_date))->toBe($expectedDuration);
});

test('subscription with multiple months extends plan accordingly', function () {
    $user = User::factory()->create();

    $startDate = now()->startOfDay();
    $endDate = $startDate->copy()->addDays(7);

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Create 3-month subscription
    $this->artisan('subscription:create', [
        'email' => $user->email,
        '--months' => 3,
    ])->assertSuccessful();

    // 3 months added on top of end_date (today + 7 days)
    $updatedPlan = $plan->fresh();
    $expectedEndDate = $endDate->copy()->addMonthsNoOverflow(3);
    $expectedDuration = (int) $startDate->diffInDays($expectedEndDate);
    expect($updatedPlan->duration_days)->toBe($expectedDuration);
    expect((int) $updatedPlan->start_date->diffInDays($updatedPlan->end_date))->toBe($expectedDuration);
});

test('handles user without active plan gracefully', function () {
    $user = User::factory()->create();

    // No plan exists
    expect($user->plans()->count())->toBe(0);

    // Create subscription should not fail
    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->fresh()->hasActiveLegacySubscription())->toBeTrue();
});

test('subscription updates only active plan not inactive ones', function () {
    $user = User::factory()->create();

    // Create inactive plan
    $inactivePlan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Old Plan',
        'status' => 'completed',
        'start_date' => now()->subMonth(),
        'end_date' => now()->subMonth()->addDays(7),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Create active plan
    $activePlan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Current Plan',
        'status' => 'active',
        'start_date' => now(),
        'end_date' => now()->addDays(7),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    // Active plan should be updated — month added on top of end_date
    $expectedEndDate = $activePlan->end_date->copy()->addMonthNoOverflow();
    $expectedDuration = (int) $activePlan->start_date->diffInDays($expectedEndDate);
    expect($activePlan->fresh()->duration_days)->toBe($expectedDuration);

    // Inactive plan should remain unchanged
    expect($inactivePlan->fresh()->duration_days)->toBe(7);
});
