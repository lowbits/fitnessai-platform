<?php

namespace App\Listeners;

use App\Events\OnboardingCompleted;
use App\Jobs\GenerateUserWorkoutPlan;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateWorkoutPlan implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OnboardingCompleted $event): void
    {
        // Dispatch job to generate workout plans
        GenerateUserWorkoutPlan::dispatch($event->user, $event->plan);
    }
}

