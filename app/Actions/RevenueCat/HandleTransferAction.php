<?php

namespace App\Actions\RevenueCat;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use NoopStudios\LaravelRevenueCat\Models\Subscription;

class HandleTransferAction
{
    public function __construct(
        private readonly AdjustActivePlanAction $adjustActivePlanAction
    ) {}

    public function execute(array $payload): void
    {
        $event = $payload['event'];

        $transferredFrom = $event['transferred_from'][0] ?? null;
        $transferredTo = $event['transferred_to'][0] ?? null;

        if (! $transferredFrom || ! $transferredTo) {
            Log::error('Transfer event missing transferred_from or transferred_to', [
                'event' => $event,
            ]);

            return;
        }

        $fromUser = User::find($transferredFrom);
        $toUser = User::find($transferredTo);

        if (! $toUser) {
            Log::error('Target user not found for transfer', [
                'transferred_to' => $transferredTo,
            ]);

            return;
        }

        $this->transferSubscriptions($fromUser, $toUser);
        $this->transferCustomer($fromUser, $toUser);
        $this->adjustPlanForNewOwner($toUser);

        Log::info('Transfer processed successfully', [
            'from_user_id' => $transferredFrom,
            'to_user_id' => $transferredTo,
        ]);
    }

    private function transferSubscriptions(?User $fromUser, User $toUser): void
    {
        if (! $fromUser) {
            Log::warning('Source user not found for transfer, skipping subscription transfer', [
                'to_user_id' => $toUser->id,
            ]);

            return;
        }

        $subscriptions = $fromUser->subscriptions()->get();

        if ($subscriptions->isEmpty()) {
            Log::info('No subscriptions to transfer', [
                'from_user_id' => $fromUser->id,
            ]);

            return;
        }

        Subscription::unguard();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'billable_id' => $toUser->id,
                'billable_type' => $toUser->getMorphClass(),
            ]);
        }

        Subscription::reguard();

        Log::info('Subscriptions transferred', [
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'count' => $subscriptions->count(),
        ]);
    }

    private function transferCustomer(?User $fromUser, User $toUser): void
    {
        if (! $fromUser) {
            return;
        }

        $customer = $fromUser->customer;

        if (! $customer) {
            return;
        }

        $customer->forceFill([
            'billable_id' => $toUser->id,
            'billable_type' => $toUser->getMorphClass(),
        ])->save();

        Log::info('Customer record transferred', [
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'revenuecat_id' => $customer->revenuecat_id,
        ]);
    }

    private function adjustPlanForNewOwner(User $toUser): void
    {
        $subscription = $toUser->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->latest('current_period_ended_at')
            ->first();

        if (! $subscription) {
            return;
        }

        $this->adjustActivePlanAction->execute(
            $toUser,
            $subscription->product_id,
            expirationAtMs: $subscription->current_period_ended_at
                ? (int) $subscription->current_period_ended_at->getTimestampMs()
                : null,
        );
    }
}
