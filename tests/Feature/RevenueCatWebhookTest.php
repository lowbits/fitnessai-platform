<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('revenuecat webhook updates subscription', function () {
    $user = User::factory()->create(['id' => 123]);
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $payload = [
        'event' => [
            'type' => 'INITIAL_PURCHASE',
            'id' => '12345678-1234-1234-1234-123456789012',
            'event_timestamp_ms' => now()->timestamp * 1000,
            'app_user_id' => (string) $user->id,
            'original_app_user_id' => '$RCAnonymousID:87c6049c58069238dce29853916d624c',
            'aliases' => [
                '$RCAnonymousID:8069238d6049ce87cc529853916d624c'
            ],
            'product_id' => 'premium_monthly',
            'period_type' => 'NORMAL',
            'purchased_at_ms' => now()->timestamp * 1000,
            'expiration_at_ms' => now()->addMonth()->timestamp * 1000,
            'environment' => 'PRODUCTION',
            'entitlement_ids' => ['pro'],
            'transaction_id' => '123456789012345',
            'original_transaction_id' => '123456789012345',
            'is_family_share' => false,
            'country_code' => 'DE',
            'currency' => 'EUR',
            'price' => 9.99,
            'price_in_purchased_currency' => 9.99,
            'subscriber_attributes' => [
                '$email' => [
                    'updated_at_ms' => now()->timestamp * 1000,
                    'value' => $user->email,
                ],
                '$displayName' => [
                    'updated_at_ms' => now()->timestamp * 1000,
                    'value' => $user->name,
                ],
            ],
            'store' => 'APP_STORE',
            'takehome_percentage' => 0.7,
            'tax_percentage' => 0.19,
            'commission_percentage' => 0.3,
        ],
        'api_version' => '1.0',
    ];

    $response = $this->postJson(route('revenue-cat.webhook'), $payload, [
        'Authorization' => 'Bearer ' . $secret,
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('customers', [
        'revenuecat_id' => (string) $user->id,
        'billable_type' => User::class,
        'billable_id' => $user->id,
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'billable_type' => User::class,
        'billable_id' => $user->id,
        'product_id' => 'premium_monthly',
        'status' => 'active',
    ]);
});

test('revenuecat webhook cancels subscription', function () {
    $user = User::factory()->create(['id' => 123]);
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    // First create an active subscription
    \NoopStudios\LaravelRevenueCat\Models\Subscription::unguard();
    $user->subscriptions()->create([
        'product_id' => 'premium_monthly',
        'name' => 'premium_monthly',
        'status' => \NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus::ACTIVE,
        'price' => '9.99',
        'currency' => 'EUR',
        'store' => 'APP_STORE',
        'current_period_started_at' => now(),
        'current_period_ended_at' => now()->addMonth(),
    ]);
    \NoopStudios\LaravelRevenueCat\Models\Subscription::reguard();

    $payload = [
        'event' => [
            'type' => 'CANCELLATION',
            'id' => 'event_id_2',
            'app_user_id' => (string) $user->id,
            'subscriber' => [
                'id' => (string) $user->id,
            ],
            'product_id' => 'premium_monthly',
            'original_transaction_id' => 'transaction_123',
            'event_timestamp_ms' => now()->timestamp * 1000,
        ],
    ];

    $response = $this->postJson(route('revenue-cat.webhook'), $payload, [
        'Authorization' => 'Bearer ' . $secret,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('subscriptions', [
        'billable_id' => $user->id,
        'status' => \NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus::CANCELED,
    ]);
});

test('revenuecat webhook requires valid auth', function () {
    config(['revenue-cat.webhook.secret' => 'valid_secret']);

    $payload = [
        'event' => [
            'type' => 'INITIAL_PURCHASE',
            'app_user_id' => '123',
        ],
    ];

    $response = $this->postJson(route('revenue-cat.webhook'), $payload, [
        'Authorization' => 'Bearer invalid_secret',
    ]);

    $response->assertStatus(401);
});
