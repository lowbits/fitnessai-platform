<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

function syncSubscription(User $user, array $overrides = []): void
{
    $subscription = $user->subscriptions()->make();
    $subscription->forceFill(array_merge([
        'name' => 'fytrr_premium_yearly_v2',
        'product_id' => 'fytrr_premium_yearly_v2',
        'currency' => 'EUR',
        'price' => '59.99',
        'status' => 'active',
        'store' => 'app_store',
        'current_period_ended_at' => now()->addYear(),
    ], $overrides));
    $subscription->save();
}

it('exposes the stored locale as settings.language so the app follows the database', function () {
    Sanctum::actingAs(User::factory()->create(['locale' => 'de']));

    getJson('/api/v3/auth/me')
        ->assertOk()
        ->assertJsonPath('settings.language', 'de');
});

it('reports free when the user has no subscription', function () {
    Sanctum::actingAs(User::factory()->create());

    getJson('/api/v3/auth/me')
        ->assertOk()
        ->assertJsonPath('subscription.active', false)
        ->assertJsonPath('subscription.status', 'free')
        ->assertJsonPath('subscription.tier', 'free')
        ->assertJsonPath('subscription.product_id', null)
        ->assertJsonPath('subscription.expires_at', null);
});

it('reports the synced yearly subscription with tier derived from the product id', function () {
    $user = User::factory()->create();
    syncSubscription($user);
    Sanctum::actingAs($user);

    getJson('/api/v3/auth/me')
        ->assertOk()
        ->assertJsonPath('subscription.active', true)
        ->assertJsonPath('subscription.status', 'active')
        ->assertJsonPath('subscription.tier', 'yearly')
        ->assertJsonPath('subscription.product_id', 'fytrr_premium_yearly_v2')
        ->assertJsonPath('subscription.will_renew', true);
});

it('reports a trialing monthly subscription as active with trial status', function () {
    $user = User::factory()->create();
    syncSubscription($user, [
        'name' => 'fytrr_premium_monthly_v2',
        'product_id' => 'fytrr_premium_monthly_v2',
        'status' => 'trial',
        'current_period_ended_at' => now()->addDays(7),
    ]);
    Sanctum::actingAs($user);

    getJson('/api/v3/auth/me')
        ->assertOk()
        ->assertJsonPath('subscription.active', true)
        ->assertJsonPath('subscription.status', 'trial')
        ->assertJsonPath('subscription.tier', 'monthly');
});

it('does not renew when the subscription is canceled', function () {
    $user = User::factory()->create();
    syncSubscription($user, ['canceled_at' => now()]);
    Sanctum::actingAs($user);

    getJson('/api/v3/auth/me')->assertOk()->assertJsonPath('subscription.will_renew', false);
});
