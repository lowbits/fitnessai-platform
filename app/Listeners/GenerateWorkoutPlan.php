<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use App\Jobs\GenerateUserWorkoutPlan;
use Illuminate\Support\Facades\Cache;

class GenerateWorkoutPlan
{
    /**
     * Handle the event.
     */
    public function __invoke(EmailVerified $event): void
    {
        $lockKey = "workout_plan_generation_{$event->plan->id}";

        if (!Cache::add($lockKey, true, now()->addMinutes(10))) {
            return;
        }

        GenerateUserWorkoutPlan::dispatch($event->user, $event->plan);
    }
}

