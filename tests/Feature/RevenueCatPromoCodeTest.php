<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('revenuecat non-renewing promo purchase grants an active subscription', function () {
    $user = User::factory()->create(['id' => 157]);
    $secret = 'test_secret';
    config(['revenue-cat.webhook.secret' => $secret]);

    $purchasedAt = now();
    $expiresAt = now()->addDays(31);

    $payload = [
        'event' => [
            'type' => 'NON_RENEWING_PURCHASE',
            'id' => 'BAF88D36-92C6-48B3-95E3-B5ACABE17EFD',
            'event_timestamp_ms' => $purchasedAt->timestamp * 1000,
            'app_user_id' => (string) $user->id,
            'original_app_user_id' => (string) $user->id,
            'aliases' => [(string) $user->id],
            'product_id' => 'rc_promo_premium_monthly',
            'period_type' => 'PROMOTIONAL',
            'purchased_at_ms' => $purchasedAt->timestamp * 1000,
            'expiration_at_ms' => $expiresAt->timestamp * 1000,
            'environment' => 'PRODUCTION',
            'entitlement_ids' => ['premium'],
            'transaction_id' => 'e54a53c384f23c12145edb446ffc6afa',
            'original_transaction_id' => 'e54a53c384f23c12145edb446ffc6afa',
            'country_code' => 'DE',
            'currency' => 'USD',
            'price' => 0.0,
            'price_in_purchased_currency' => 0.0,
            'store' => 'PROMOTIONAL',
        ],
        'api_version' => '1.0',
    ];

    $response = $this->postJson(route('revenue-cat.webhook'), $payload, [
        'Authorization' => 'Bearer '.$secret,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('subscriptions', [
        'billable_type' => User::class,
        'billable_id' => $user->id,
        'product_id' => 'rc_promo_premium_monthly',
        'status' => 'active',
    ]);

    expect($user->fresh()->hasPaidSubscription())->toBeTrue();
});
