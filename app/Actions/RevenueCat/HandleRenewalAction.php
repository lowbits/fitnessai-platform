<?php

namespace App\Actions\RevenueCat;

use App\Events\RevenueCat\SubscriptionRenewed;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

class HandleRenewalAction
{
    public function execute(array $payload): void
    {
        $event = $payload['event'];

        $user = $this->findUser($event);

        if (!$user) {
            return;
        }

        $this->syncSubscription($user, $event);

        SubscriptionRenewed::dispatch($user, $event);

        Log::info('Subscription renewal processed successfully', [
            'user_id' => $user->id,
            'product_id' => $event['product_id'],
            'expiration_at' => $event['expiration_at_ms'] ?? null,
        ]);
    }

    private function findUser(array $event): ?User
    {
        $user = User::find($event['app_user_id']);

        if (!$user) {
            Log::error('User not found for renewal', [
                'app_user_id' => $event['app_user_id'],
                'event_type' => $event['type'],
            ]);
        }

        return $user;
    }

    private function syncSubscription(User $user, array $event): void
    {
        $subscription = $user->subscriptions()
            ->where('product_id', $event['product_id'])
            ->first();

        if (!$subscription) {
            Log::warning('Subscription not found for renewal, creating new one', [
                'user_id' => $user->id,
                'product_id' => $event['product_id'],
            ]);

            // If subscription doesn't exist, create it
            $subscriptionData = $this->extractSubscriptionData($event);

            Subscription::unguard();
            $user->subscriptions()->create($subscriptionData);
            Subscription::reguard();

            return;
        }

        // Update existing subscription
        $subscriptionData = $this->extractSubscriptionData($event);

        Subscription::unguard();
        $subscription->update($subscriptionData);
        Subscription::reguard();

        Log::info('Subscription renewed', [
            'user_id' => $user->id,
            'product_id' => $event['product_id'],
            'new_period_end' => $subscriptionData['current_period_ended_at'],
        ]);
    }

    private function extractSubscriptionData(array $event): array
    {
        return [
            'name' => $event['product_id'],
            'product_id' => $event['product_id'],
            'currency' => $event['currency'] ?? 'EUR',
            'price' => (string) ($event['price_in_purchased_currency'] ?? $event['price'] ?? '0'),
            'status' => SubscriptionStatus::ACTIVE,
            'store' => $event['store'] ?? 'APP_STORE',
            'current_period_started_at' => $this->parseMilliseconds($event['period_start_at_ms'] ?? $event['purchased_at_ms'] ?? null),
            'current_period_ended_at' => $this->parseMilliseconds($event['expiration_at_ms'] ?? null),
        ];
    }

    private function parseMilliseconds(?int $milliseconds): ?Carbon
    {
        if (!$milliseconds) {
            return null;
        }

        try {
            return Carbon::createFromTimestampMs($milliseconds);
        } catch (\Exception $e) {
            Log::warning('Failed to parse milliseconds timestamp', [
                'milliseconds' => $milliseconds,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

