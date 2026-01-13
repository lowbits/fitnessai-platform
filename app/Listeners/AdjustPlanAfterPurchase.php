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
        $this->adjustActivePlanAction->execute(
            $event->user,
            $event->event['product_id']
        );
    }
}
