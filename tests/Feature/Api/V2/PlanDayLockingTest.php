<?php

use App\Models\Plan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

use function Pest\Laravel\getJson;

function createSubscription(User $user, SubscriptionStatus $status = SubscriptionStatus::ACTIVE): void
{
    Subscription::unguard();
    $user->subscriptions()->create([
        'name' => 'default',
        'store' => 'APP_STORE',
        'currency' => 'EUR',
        'price' => '9.99',
        'status' => $status,
        'product_id' => 'rc_premium_monthly',
        'current_period_started_at' => now(),
        'current_period_ended_at' => now()->addMonth(),
    ]);
    Subscription::reguard();
}

test('day 1 is unlocked for free users', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user);

    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now(),
        'duration_days' => 7,
    ]);

    $response = getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertOk()
        ->assertJsonPath('locked', false)
        ->assertJsonPath('plan_day', 1);
});

test('days 2-7 are unlocked for users on free trial', function () {
    $user = User::factory()->onTrial()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user);

    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(1),
        'duration_days' => 7,
    ]);

    $response = getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertOk()
        ->assertJsonPath('locked', false)
        ->assertJsonPath('plan_day', 2);
});

test('days 2-7 are locked for users with expired trial and no subscription', function () {
    $user = User::factory()->trialExpired()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user);

    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(1),
        'duration_days' => 7,
    ]);

    $response = getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertOk()
        ->assertJsonPath('locked', true)
        ->assertJsonPath('plan_day', 2);
});

test('days 2-7 are unlocked for users with active subscription', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user);

    createSubscription($user, SubscriptionStatus::ACTIVE);

    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(1),
        'duration_days' => 7,
    ]);

    $response = getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertOk()
        ->assertJsonPath('locked', false)
        ->assertJsonPath('plan_day', 2);
});

test('days 2-7 are unlocked for users with trial subscription', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user);

    createSubscription($user, SubscriptionStatus::TRIAL);

    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(1),
        'duration_days' => 7,
    ]);

    $response = getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertOk()
        ->assertJsonPath('locked', false)
        ->assertJsonPath('plan_day', 2);
});

test('locked days still return meal and workout data for blurred preview', function () {
    $user = User::factory()->trialExpired()->create(['email_verified_at' => now()]);
    Sanctum::actingAs($user);

    Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(1),
        'duration_days' => 7,
    ]);

    $response = getJson('/api/v2/plan/day/'.now()->format('Y-m-d'));

    $response->assertOk()
        ->assertJsonPath('locked', true)
        ->assertJsonStructure(['meals', 'workout', 'daily_totals']);
});
