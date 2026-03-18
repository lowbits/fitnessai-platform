<?php

namespace App\Actions\RevenueCat;

use App\Events\RevenueCat\InitialPurchaseProcessed;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use NoopStudios\LaravelRevenueCat\Enums\SubscriptionStatus;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

class HandleInitialPurchaseAction
{
    public function execute(array $payload): void
    {
        $event = $payload['event'];

        $user = $this->findUser($event);

        if (! $user) {
            return;
        }

        $this->syncCustomer($user, $event);
        $this->syncSubscription($user, $event);

        InitialPurchaseProcessed::dispatch($user, $event);

        Log::info('Initial purchase processed successfully', [
            'user_id' => $user->id,
            'product_id' => $event['product_id'],
        ]);
    }

    private function findUser(array $event): ?User
    {
        $user = User::find($event['app_user_id']);

        if (! $user) {
            Log::error('User not found for initial purchase', [
                'app_user_id' => $event['app_user_id'],
                'event_type' => $event['type'],
            ]);
        }

        return $user;
    }

    private function syncCustomer(User $user, array $event): void
    {
        $attributes = $event['subscriber_attributes'] ?? [];

        $user->customer()->updateOrCreate(
            ['revenuecat_id' => $event['app_user_id']],
            [
                'email' => data_get($attributes, '$email.value', $user->email),
                'display_name' => data_get($attributes, '$displayName.value', $user->name),
                'phone_number' => data_get($attributes, '$phoneNumber.value'),
                'metadata' => $this->buildCustomerMetadata($event),
            ]
        );
    }

    private function syncSubscription(User $user, array $event): void
    {
        $subscriptionData = $this->extractSubscriptionData($event);

        Subscription::unguard();

        $user->subscriptions()->updateOrCreate(
            ['product_id' => $event['product_id']],
            $subscriptionData
        );

        Subscription::reguard();
    }

    private function extractSubscriptionData(array $event): array
    {
        $isTrial = ! empty($event['is_trial_period']);

        $data = [
            'name' => $event['product_id'],
            'currency' => $event['currency'] ?? 'EUR',
            'price' => (string) ($event['price_in_purchased_currency'] ?? $event['price'] ?? '0'),
            'status' => $isTrial ? SubscriptionStatus::TRIAL : SubscriptionStatus::ACTIVE,
            'store' => $event['store'] ?? 'APP_STORE',
            'current_period_started_at' => $this->parseMilliseconds($event['purchased_at_ms'] ?? null),
            'current_period_ended_at' => $this->parseMilliseconds($event['expiration_at_ms'] ?? null),
        ];

        if ($isTrial) {
            $data['trial_started_at'] = $this->parseMilliseconds($event['purchased_at_ms'] ?? null);
            $data['trial_ended_at'] = $this->parseMilliseconds($event['expiration_at_ms'] ?? null);
        }

        return $data;
    }

    private function buildCustomerMetadata(array $event): array
    {
        return [
            'original_app_user_id' => $event['original_app_user_id'] ?? null,
            'aliases' => $event['aliases'] ?? [],
            'country_code' => $event['country_code'] ?? null,
            'environment' => $event['environment'] ?? null,
            'entitlement_ids' => $event['entitlement_ids'] ?? [],
            'transaction_id' => $event['transaction_id'] ?? null,
            'original_transaction_id' => $event['original_transaction_id'] ?? null,
            'is_family_share' => $event['is_family_share'] ?? false,
            'store' => $event['store'] ?? null,
        ];
    }

    private function parseMilliseconds(?int $milliseconds): ?Carbon
    {
        if (! $milliseconds) {
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
