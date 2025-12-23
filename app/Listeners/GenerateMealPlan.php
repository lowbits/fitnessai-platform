<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use App\Jobs\GenerateUserMealPlan;
use Illuminate\Support\Facades\Cache;

class GenerateMealPlan
{
    /**
     * Handle the event.
     */
    public function __invoke(EmailVerified $event): void
    {
        $lockKey = "meal_plan_generation_{$event->plan->id}";

        if (!Cache::add($lockKey, true, now()->addMinutes(10))) {
            return;
        }

        GenerateUserMealPlan::dispatch($event->user, $event->plan)->onQueue('nutrition');
    }
}
