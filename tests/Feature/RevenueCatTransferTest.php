<?php

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

uses(RefreshDatabase::class);

function createSubscriptionForUser(User $user, array $overrides = []): Subscription
{
    Subscription::unguard();
    $subscription = $user->subscriptions()->create(array_merge([
        'product_id' => 'fytrr_premium_monthly_v2',
        'name' => 'fytrr_premium_monthly_v2',
        'status' => SubscriptionStatus::ACTIVE,
        'price' => '9.99',
        'currency' => 'EUR',
        'store' => 'APP_STORE',
        'current_period_started_at' => now(),
        'current_period_ended_at' => now()->addMonth(),
    ], $overrides));
    Subscription::reguard();

    return $subscription;
}

function transferPayload(string $fromUserId, string $toUserId): array
{
    return [
        'event' => [
            'type' => 'TRANSFER',
            'id' => fake()->uuid(),
            'event_timestamp_ms' => now()->getTimestampMs(),
            'transferred_from' => [$fromUserId],
            'transferred_to' => [$toUserId],
            'subscriber_attributes' => [],
            'store' => 'APP_STORE',
            'environment' => 'SANDBOX',
            'app_id' => 'test_app',
        ],
        'api_version' => '1.0',
    ];
}

test('transfer moves subscription from old user to new user', function () {
    $fromUser = User::factory()->create();
    $toUser = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    createSubscriptionForUser($fromUser);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        transferPayload((string) $fromUser->id, (string) $toUser->id),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);

    $this->assertDatabaseHas('subscriptions', [
        'billable_id' => $toUser->id,
        'billable_type' => User::class,
        'product_id' => 'fytrr_premium_monthly_v2',
    ]);

    $this->assertDatabaseMissing('subscriptions', [
        'billable_id' => $fromUser->id,
    ]);
});

test('transfer moves customer record to new user', function () {
    $fromUser = User::factory()->create();
    $toUser = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $fromUser->customer()->create([
        'revenuecat_id' => (string) $fromUser->id,
        'email' => $fromUser->email,
    ]);
    createSubscriptionForUser($fromUser);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        transferPayload((string) $fromUser->id, (string) $toUser->id),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);

    $this->assertDatabaseHas('customers', [
        'revenuecat_id' => (string) $fromUser->id,
        'billable_id' => $toUser->id,
        'billable_type' => User::class,
    ]);
});

test('transfer adjusts active plan for new owner', function () {
    $fromUser = User::factory()->create();
    $toUser = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $expiresAt = now()->addMonth();
    createSubscriptionForUser($fromUser, [
        'current_period_ended_at' => $expiresAt,
    ]);

    Plan::factory()->active()->create([
        'user_id' => $toUser->id,
        'start_date' => now()->subWeek(),
        'end_date' => now()->addDays(3),
        'duration_days' => 10,
    ]);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        transferPayload((string) $fromUser->id, (string) $toUser->id),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);

    $plan = $toUser->plans()->where('status', 'active')->first();
    expect($plan->end_date->startOfDay()->toDateString())
        ->toBe($expiresAt->startOfDay()->toDateString());
});

test('transfer adjusts the plan using the subscription with the latest period end', function () {
    $fromUser = User::factory()->create();
    $toUser = User::factory()->create();
    config(['revenue-cat.webhook.secret' => 'test_secret']);

    // Target already has a short sub first, then a long one — the long one must win.
    createSubscriptionForUser($toUser, ['current_period_ended_at' => now()->addDays(5)]);
    $longEnd = now()->addDays(400);
    createSubscriptionForUser($toUser, ['current_period_ended_at' => $longEnd]);

    // The transferred sub is also short, so it must not decide the plan length.
    createSubscriptionForUser($fromUser, ['current_period_ended_at' => now()->addDays(5)]);

    Plan::factory()->active()->create([
        'user_id' => $toUser->id,
        'start_date' => now()->subWeek(),
        'end_date' => now()->addDays(3),
        'duration_days' => 10,
    ]);

    $this->postJson(
        route('revenue-cat.webhook'),
        transferPayload((string) $fromUser->id, (string) $toUser->id),
        ['Authorization' => 'Bearer test_secret']
    )->assertStatus(200);

    $plan = $toUser->plans()->where('status', 'active')->first();
    expect($plan->end_date->startOfDay()->toDateString())
        ->toBe($longEnd->startOfDay()->toDateString());
});

test('transfer handles missing source user gracefully', function () {
    $toUser = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        transferPayload('99999', (string) $toUser->id),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);
});

test('transfer handles missing target user', function () {
    $fromUser = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    createSubscriptionForUser($fromUser);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        transferPayload((string) $fromUser->id, '99999'),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);

    // Subscription should remain on fromUser since target doesn't exist
    $this->assertDatabaseHas('subscriptions', [
        'billable_id' => $fromUser->id,
    ]);
});
