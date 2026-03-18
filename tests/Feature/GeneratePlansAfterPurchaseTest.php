<?php

use App\Events\RevenueCat\InitialPurchaseProcessed;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Listeners\GeneratePlansAfterPurchase;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('initial purchase triggers plan generation for new user without email verification', function () {
    Bus::fake();

    $user = User::factory()->create();

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => now()->startOfDay(),
        'end_date' => now()->addDays(7)->startOfDay(),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
        'generation_completed_at' => null,
    ]);

    $event = new InitialPurchaseProcessed($user, [
        'type' => 'INITIAL_PURCHASE',
        'app_user_id' => (string) $user->id,
        'product_id' => 'premium_monthly',
    ]);

    app(GeneratePlansAfterPurchase::class)->handle($event);

    Bus::assertDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertDispatched(GenerateUserMealPlan::class);
});

test('initial purchase does not trigger generation if plans already generated', function () {
    Bus::fake();

    $user = User::factory()->create();

    Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => now()->startOfDay(),
        'end_date' => now()->addDays(7)->startOfDay(),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
        'generation_completed_at' => now(),
    ]);

    $event = new InitialPurchaseProcessed($user, [
        'type' => 'INITIAL_PURCHASE',
        'app_user_id' => (string) $user->id,
        'product_id' => 'premium_monthly',
    ]);

    app(GeneratePlansAfterPurchase::class)->handle($event);

    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertNotDispatched(GenerateUserMealPlan::class);
});

test('initial purchase uses cache locks to prevent duplicate generation', function () {
    Bus::fake();

    $user = User::factory()->create();

    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => now()->startOfDay(),
        'end_date' => now()->addDays(7)->startOfDay(),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
        'generation_completed_at' => null,
    ]);

    // Simulate locks already held (e.g. from email verification)
    Cache::put("workout_plan_generation_{$plan->id}", true, now()->addMinutes(10));
    Cache::put("meal_plan_generation_{$plan->id}", true, now()->addMinutes(10));

    $event = new InitialPurchaseProcessed($user, [
        'type' => 'INITIAL_PURCHASE',
        'app_user_id' => (string) $user->id,
        'product_id' => 'premium_monthly',
    ]);

    app(GeneratePlansAfterPurchase::class)->handle($event);

    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertNotDispatched(GenerateUserMealPlan::class);
});
