<?php

namespace App\Actions\RevenueCat;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

class HandleProductChangeAction
{
    public function __construct(
        private readonly AdjustActivePlanAction $adjustActivePlanAction
    ) {}

    public function execute(array $payload): void
    {
        $event = $payload['event'];

        $user = User::find($event['app_user_id']);

        if (! $user) {
            Log::error('User not found for product change', [
                'app_user_id' => $event['app_user_id'],
            ]);

            return;
        }

        $oldProductId = $event['product_id'];
        $newProductId = $event['new_product_id'] ?? null;

        if (! $newProductId) {
            Log::warning('Product change event missing new_product_id', [
                'user_id' => $user->id,
                'event' => $event,
            ]);

            return;
        }

        $this->updateSubscription($user, $oldProductId, $newProductId);
        $this->adjustPlan($user, $newProductId);

        Log::info('Product change processed successfully', [
            'user_id' => $user->id,
            'old_product_id' => $oldProductId,
            'new_product_id' => $newProductId,
        ]);
    }

    private function updateSubscription(User $user, string $oldProductId, string $newProductId): void
    {
        $subscription = $user->subscriptions()
            ->where('product_id', $oldProductId)
            ->first();

        if (! $subscription) {
            Log::warning('No subscription found for product change', [
                'user_id' => $user->id,
                'old_product_id' => $oldProductId,
            ]);

            return;
        }

        Subscription::unguard();

        $subscription->update([
            'name' => $newProductId,
            'product_id' => $newProductId,
        ]);

        Subscription::reguard();

        Log::info('Subscription product updated', [
            'user_id' => $user->id,
            'old_product_id' => $oldProductId,
            'new_product_id' => $newProductId,
        ]);
    }

    private function adjustPlan(User $user, string $newProductId): void
    {
        // Don't pass expiration_at_ms — it refers to the OLD product's expiration.
        // Let AdjustActivePlanAction calculate based on the new product ID.
        $this->adjustActivePlanAction->execute(
            $user,
            $newProductId,
        );
    }
}
