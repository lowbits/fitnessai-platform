<?php

namespace App\Listeners;

use App\Actions\RevenueCat\AdjustActivePlanAction;
use App\Events\RevenueCat\SubscriptionRenewed;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdjustPlanAfterRenewal implements ShouldQueue
{
    public function __construct(
        private readonly AdjustActivePlanAction $adjustActivePlanAction
    ) {}

    public function handle(SubscriptionRenewed $event): void
    {
        $this->adjustActivePlanAction->execute(
            $event->user,
            $event->eventData['product_id']
        );
    }
}

