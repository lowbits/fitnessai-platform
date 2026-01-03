<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

test('can create beta subscription for user', function () {
    $user = User::factory()->create();

    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->fresh()->hasActiveSubscription())->toBeTrue();
});

test('subscription extends existing plan from 7 to 30 days', function () {
    // Create user with 7-day plan
    $user = User::factory()->create();

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
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

    expect($plan->duration_days)->toBe(7);
    expect((int) $plan->start_date->diffInDays($plan->end_date))->toBe(7);

    // Create subscription
    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    // Check plan is updated to 30 days
    $updatedPlan = $plan->fresh();
    expect($updatedPlan->duration_days)->toBe(30);
    expect((int) $updatedPlan->start_date->diffInDays($updatedPlan->end_date))->toBe(30);
});

test('subscription with multiple months extends plan accordingly', function () {
    $user = User::factory()->create();

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
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

    // Create 3-month subscription
    $this->artisan('subscription:create', [
        'email' => $user->email,
        '--months' => 3,
    ])->assertSuccessful();

    // Check plan is updated to 90 days (3 months * 30 days)
    $updatedPlan = $plan->fresh();
    expect($updatedPlan->duration_days)->toBe(90);
    expect((int) $updatedPlan->start_date->diffInDays($updatedPlan->end_date))->toBe(90);
});

test('handles user without active plan gracefully', function () {
    $user = User::factory()->create();

    // No plan exists
    expect($user->plans()->count())->toBe(0);

    // Create subscription should not fail
    $this->artisan('subscription:create', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->fresh()->hasActiveSubscription())->toBeTrue();
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

    // Active plan should be updated
    expect($activePlan->fresh()->duration_days)->toBe(30);

    // Inactive plan should remain unchanged
    expect($inactivePlan->fresh()->duration_days)->toBe(7);
});
