<?php

namespace App\Listeners;

use App\Actions\RevenueCat\AdjustActivePlanAction;
use App\Events\RevenueCat\InitialPurchaseProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdjustPlanAfterPurchase implements ShouldQueue
{
    public function __construct(
        private readonly AdjustActivePlanAction $adjustActivePlanAction
    ) {}

    public function handle(InitialPurchaseProcessed $event): void
    {
        $expirationAtMs = ($event->event['period_type'] ?? null) === 'TRIAL'
            ? ($event->event['expiration_at_ms'] ?? null)
            : null;

        $this->adjustActivePlanAction->execute(
            $event->user,
            $event->event['product_id'],
            expirationAtMs: $expirationAtMs,
        );
    }
}
