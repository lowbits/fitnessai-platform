<?php

namespace Tests\Feature\Api\V2;

use App\Models\Plan;
use App\Models\SubscriptionLegacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class MeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_endpoint_returns_revenuecat_subscription_if_active()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create RevenueCat subscription
        $user->subscriptions()->forceCreate([
            'billable_id' => $user->id,
            'billable_type' => $user->getMorphClass(),
            'name' => 'main',
            'product_id' => 'pro_monthly',
            'status' => 'active',
            'price' => '9.99',
            'currency' => 'USD',
            'store' => 'app_store',
            'current_period_ended_at' => now()->addMonth(),
        ]);

        $response = $this->getJson('/api/v2/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('subscription.status', 'active')
            ->assertJsonPath('subscription.tier', 'pro_monthly');
    }

    public function test_me_endpoint_returns_legacy_subscription_if_no_revenuecat_but_legacy_active()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create legacy subscription
        SubscriptionLegacy::create([
            'user_id' => $user->id,
            'type' => 'beta',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $response = $this->getJson('/api/v2/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('subscription.status', 'active')
            ->assertJsonPath('subscription.tier', 'Ambassador');
    }

    public function test_me_endpoint_returns_free_if_no_active_subscriptions()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v2/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('subscription.status', 'free')
            ->assertJsonPath('subscription.tier', 'free');
    }
}
