<?php

namespace App\Listeners;

use App\Events\OnboardingCompleted;
use App\Jobs\GenerateUserMealPlan;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateMealPlan implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OnboardingCompleted $event): void
    {
        // Dispatch job to generate meal plans for all 28 days
        GenerateUserMealPlan::dispatch($event->user, $event->plan);
    }
}
