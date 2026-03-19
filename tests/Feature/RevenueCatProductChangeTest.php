<?php

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

uses(RefreshDatabase::class);

function productChangePayload(string $userId, string $oldProduct, string $newProduct, ?int $expirationAtMs = null): array
{
    return [
        'event' => [
            'type' => 'PRODUCT_CHANGE',
            'id' => fake()->uuid(),
            'event_timestamp_ms' => now()->getTimestampMs(),
            'app_user_id' => $userId,
            'original_app_user_id' => $userId,
            'aliases' => [$userId],
            'product_id' => $oldProduct,
            'new_product_id' => $newProduct,
            'period_type' => 'TRIAL',
            'purchased_at_ms' => now()->getTimestampMs(),
            'expiration_at_ms' => $expirationAtMs ?? now()->addYear()->getTimestampMs(),
            'environment' => 'SANDBOX',
            'entitlement_ids' => ['premium'],
            'transaction_id' => fake()->numerify('############'),
            'original_transaction_id' => fake()->numerify('############'),
            'is_family_share' => false,
            'country_code' => 'DE',
            'currency' => 'EUR',
            'price' => 0.0,
            'price_in_purchased_currency' => 0.0,
            'store' => 'APP_STORE',
        ],
        'api_version' => '1.0',
    ];
}

test('product change updates subscription product id', function () {
    $user = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    Subscription::unguard();
    $user->subscriptions()->create([
        'product_id' => 'fytrr_premium_monthly_v2',
        'name' => 'fytrr_premium_monthly_v2',
        'status' => SubscriptionStatus::ACTIVE,
        'price' => '9.99',
        'currency' => 'EUR',
        'store' => 'APP_STORE',
        'current_period_started_at' => now(),
        'current_period_ended_at' => now()->addMonth(),
    ]);
    Subscription::reguard();

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        productChangePayload(
            (string) $user->id,
            'fytrr_premium_monthly_v2',
            'fytrr_premium_yearly_v2'
        ),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);

    $this->assertDatabaseHas('subscriptions', [
        'billable_id' => $user->id,
        'product_id' => 'fytrr_premium_yearly_v2',
        'name' => 'fytrr_premium_yearly_v2',
    ]);

    $this->assertDatabaseMissing('subscriptions', [
        'billable_id' => $user->id,
        'product_id' => 'fytrr_premium_monthly_v2',
    ]);
});

test('product change adjusts active plan duration', function () {
    $user = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    Subscription::unguard();
    $user->subscriptions()->create([
        'product_id' => 'fytrr_premium_monthly_v2',
        'name' => 'fytrr_premium_monthly_v2',
        'status' => SubscriptionStatus::ACTIVE,
        'price' => '9.99',
        'currency' => 'EUR',
        'store' => 'APP_STORE',
        'current_period_started_at' => now(),
        'current_period_ended_at' => now()->addMonth(),
    ]);
    Subscription::reguard();

    $plan = Plan::factory()->active()->create([
        'user_id' => $user->id,
        'start_date' => now()->subWeek(),
        'end_date' => now()->addMonth(),
        'duration_days' => 37,
    ]);

    $expiresAt = now()->addYear();

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        productChangePayload(
            (string) $user->id,
            'fytrr_premium_monthly_v2',
            'fytrr_premium_yearly_v2',
            (int) $expiresAt->getTimestampMs()
        ),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);

    $plan->refresh();
    expect($plan->end_date->startOfDay()->toDateString())
        ->toBe($expiresAt->startOfDay()->toDateString());
});

test('product change handles missing user gracefully', function () {
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        productChangePayload('99999', 'fytrr_premium_monthly_v2', 'fytrr_premium_yearly_v2'),
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);
});

test('product change handles missing new_product_id gracefully', function () {
    $user = User::factory()->create();
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $payload = productChangePayload(
        (string) $user->id,
        'fytrr_premium_monthly_v2',
        'fytrr_premium_yearly_v2'
    );
    unset($payload['event']['new_product_id']);

    $response = $this->postJson(
        route('revenue-cat.webhook'),
        $payload,
        ['Authorization' => 'Bearer '.$secret]
    );

    $response->assertStatus(200);
});
