<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;

/**
 * The user's subscription, read from the RevenueCat-synced subscriptions table.
 * Entitlement (`active`), lifecycle (`status`) and billing (`product_id`/`tier`)
 * are kept as separate fields; a user without a subscription reports free.
 *
 * @mixin \App\Models\User
 */
class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subscription = $this->subscriptions()
            ->whereIn('status', [SubscriptionStatus::ACTIVE, SubscriptionStatus::TRIAL])
            ->latest('current_period_ended_at')
            ->first();

        if (! $subscription) {
            return [
                'active' => false,
                'status' => 'free',
                'tier' => 'free',
                'product_id' => null,
                'expires_at' => null,
                'will_renew' => false,
            ];
        }

        return [
            'active' => true,
            'status' => $subscription->status,
            'tier' => $this->tierFor($subscription->product_id),
            'product_id' => $subscription->product_id,
            'expires_at' => $subscription->current_period_ended_at,
            'will_renew' => $subscription->canceled_at === null,
        ];
    }

    private function tierFor(?string $productId): ?string
    {
        if (! $productId) {
            return null;
        }

        $id = strtolower($productId);

        return match (true) {
            str_contains($id, 'year'), str_contains($id, 'annual') => 'yearly',
            str_contains($id, 'month') => 'monthly',
            default => null,
        };
    }
}
