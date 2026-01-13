<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Events\RevenueCat\InitialPurchaseProcessed;
use App\Listeners\AdjustPlanAfterPurchase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('initial purchase adjusts plan duration and triggers generation if behind', function () {
    Bus::fake();
    Event::fake([InitialPurchaseProcessed::class]);

    $user = User::factory()->create(['id' => 123]);

    // Create an active 7-day plan that started 5 days ago
    // Generation day is usually 3 days after start (so it was 2 days ago)
    $startDate = now()->subDays(5)->startOfDay();
    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $startDate->copy()->addDays(7),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Generate workout plans for the first 7 days
    for ($i = 0; $i < 7; $i++) {
        WorkoutPlan::create([
            'plan_id' => $plan->id,
            'date' => $startDate->copy()->addDays($i),
            'day_number' => $i + 1,
            'status' => 'generated',
            'workout_name' => 'Workout ' . ($i + 1),
        ]);
    }

    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $payload = [
        'event' => [
            'type' => 'INITIAL_PURCHASE',
            'id' => '12345678-1234-1234-1234-123456789012',
            'event_timestamp_ms' => now()->timestamp * 1000,
            'app_user_id' => (string) $user->id,
            'product_id' => 'premium_monthly',
            'purchased_at_ms' => now()->timestamp * 1000,
            'expiration_at_ms' => now()->addMonth()->timestamp * 1000,
            'environment' => 'PRODUCTION',
            'store' => 'APP_STORE',
        ],
        'api_version' => '1.0',
    ];

    $response = $this->postJson(route('revenue-cat.webhook'), $payload, [
        'Authorization' => 'Bearer ' . $secret,
    ]);

    $response->assertStatus(200);

    // 1. Verify Event was dispatched
    Event::assertDispatched(InitialPurchaseProcessed::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });

    // 2. Manually trigger the listener logic since it's queued
    app(AdjustPlanAfterPurchase::class)->handle(new InitialPurchaseProcessed($user, $payload['event']));

    // 3. Check plan duration is adjusted accordingly
    $plan->refresh();
    $expectedDuration = (int) $plan->start_date->diffInDays($plan->start_date->copy()->addMonth());
    expect($plan->duration_days)->toBe($expectedDuration);
    expect((int)$plan->start_date->diffInDays($plan->end_date))->toBe($expectedDuration);

    // 4. Check if generation jobs were dispatched
    // Since it's day 5 and generation should happen around day 3 or 4,
    // and we only have plans until day 7, it should trigger generation.
    Bus::assertDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertDispatched(GenerateUserMealPlan::class);
});

test('initial purchase does not trigger generation if not behind', function () {
    Bus::fake();
    Event::fake([InitialPurchaseProcessed::class]);

    $user = User::factory()->create(['id' => 456]);

    // Plan started today (Day 0)
    $startDate = now()->startOfDay();
    $plan = Plan::create([
        'user_id' => $user->id,
        'plan_name' => 'Test Plan',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $startDate->copy()->addDays(7),
        'duration_days' => 7,
        'daily_calories' => 2000,
        'daily_protein_g' => 150,
        'daily_carbs_g' => 200,
        'daily_fat_g' => 60,
        'workouts_per_week' => 3,
    ]);

    // Generate workout plans for the first 7 days
    for ($i = 0; $i < 7; $i++) {
        WorkoutPlan::create([
            'plan_id' => $plan->id,
            'date' => $startDate->copy()->addDays($i),
            'day_number' => $i + 1,
            'status' => 'generated',
            'workout_name' => 'Workout ' . ($i + 1),
        ]);
    }

    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $payload = [
        'event' => [
            'type' => 'INITIAL_PURCHASE',
            'app_user_id' => (string) $user->id,
            'product_id' => 'premium_monthly',
        ],
        'api_version' => '1.0',
    ];

    $this->postJson(route('revenue-cat.webhook'), $payload, [
        'Authorization' => 'Bearer ' . $secret,
    ])->assertStatus(200);

    // 1. Verify Event was dispatched
    Event::assertDispatched(InitialPurchaseProcessed::class);

    // 2. Manually trigger the listener logic
    app(AdjustPlanAfterPurchase::class)->handle(new InitialPurchaseProcessed($user, $payload['event']));

    $plan->refresh();
    $expectedDuration = (int) $plan->start_date->diffInDays($plan->start_date->copy()->addMonth());
    expect($plan->duration_days)->toBe($expectedDuration);

    // Should NOT dispatch because it's only Day 0 and we have 7 days of plans (6 days in future)
    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertNotDispatched(GenerateUserMealPlan::class);
});
