<?php

namespace App\Listeners;

use App\Actions\Plan\StartPlanGeneration;
use App\Events\AiConsentGranted;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Plan generation only starts once the user has consented to AI processing, so
 * we trigger it here rather than at signup for consent-capable clients.
 */
class StartPlanGenerationOnConsent implements ShouldQueue
{
    public function __construct(private readonly StartPlanGeneration $startPlanGeneration) {}

    public function handle(AiConsentGranted $event): void
    {
        $this->startPlanGeneration->execute($event->user);
    }
}
