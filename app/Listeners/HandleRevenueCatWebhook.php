<?php

namespace App\Listeners;

use App\Actions\RevenueCat\HandleInitialPurchaseAction;
use App\Actions\RevenueCat\HandleProductChangeAction;
use App\Actions\RevenueCat\HandleRenewalAction;
use App\Actions\RevenueCat\HandleTransferAction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use NoopStudios\LaravelRevenueCat\Events\WebhookReceived;

class HandleRevenueCatWebhook
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly HandleInitialPurchaseAction $initialPurchaseAction,
        private readonly HandleRenewalAction $renewalAction,
        private readonly HandleTransferAction $transferAction,
        private readonly HandleProductChangeAction $productChangeAction,
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type = $payload['event']['type'];

        Log::info('RevenueCat webhook received', [
            'type' => $type,
            'payload' => $payload,
        ]);

        switch ($type) {
            case 'INITIAL_PURCHASE':
                $this->initialPurchaseAction->execute($payload);
                break;
            case 'RENEWAL':
                $this->handleRenewal($payload);
                break;
            case 'CANCELLATION':
                $this->handleCancellation($payload);
                break;
            case 'NON_RENEWING_PURCHASE':
                $this->initialPurchaseAction->execute($payload);
                break;
            case 'SUBSCRIPTION_PAUSED':
                $this->handleSubscriptionPaused($payload);
                break;
            case 'SUBSCRIPTION_RESUMED':
                $this->handleSubscriptionResumed($payload);
                break;
            case 'PRODUCT_CHANGE':
                $this->productChangeAction->execute($payload);
                break;
            case 'TRANSFER':
                $this->transferAction->execute($payload);
                break;
            case 'BILLING_ISSUE':
                $this->handleBillingIssue($payload);
                break;
            case 'REFUND':
                $this->handleRefund($payload);
                break;
            case 'EXPIRATION':
                $this->handleExpiration($payload);
                break;
            case 'SUBSCRIPTION_PERIOD_CHANGED':
                $this->handleSubscriptionPeriodChanged($payload);
                break;
        }
    }

    protected function handleRenewal(array $payload): void
    {
        $this->renewalAction->execute($payload);
    }

    protected function handleCancellation(array $payload): void
    {
        Log::info('Handling cancellation', ['payload' => $payload]);

        $event = $payload['event'];
        $user = User::find($event['app_user_id']);

        if (! $user) {
            Log::error('User not found for cancellation', [
                'app_user_id' => $event['app_user_id'],
            ]);

            return;
        }

        $subscription = $user->subscriptions()
            ->where('product_id', $event['product_id'])
            ->first();

        if ($subscription) {
            $subscription->cancel();
            Log::info('Subscription cancelled successfully', [
                'user_id' => $user->id,
                'product_id' => $event['product_id'],
            ]);
        }
    }

    protected function handleSubscriptionPaused(array $payload): void
    {
        Log::info('Handling subscription paused', ['payload' => $payload]);
    }

    protected function handleSubscriptionResumed(array $payload): void
    {
        Log::info('Handling subscription resumed', ['payload' => $payload]);
    }

    protected function handleBillingIssue(array $payload): void
    {
        Log::info('Handling billing issue', ['payload' => $payload]);
    }

    protected function handleExpiration(array $payload): void
    {
        $event = $payload['event'];
        $user = User::find($event['app_user_id']);

        if (! $user) {
            Log::error('User not found for expiration', [
                'app_user_id' => $event['app_user_id'],
            ]);

            return;
        }

        $subscription = $user->subscriptions()
            ->where('product_id', $event['product_id'])
            ->first();

        if ($subscription) {
            $subscription->markAsExpired();
            Log::info('Subscription expired', [
                'user_id' => $user->id,
                'product_id' => $event['product_id'],
            ]);
        }
    }

    protected function handleRefund(array $payload): void
    {
        Log::info('Handling refund', ['payload' => $payload]);
    }

    protected function handleSubscriptionPeriodChanged(array $payload): void
    {
        Log::info('Handling subscription period changed', ['payload' => $payload]);
    }
}
