<?php

namespace App\Listeners;

use App\Events\RevenueCat\InitialPurchaseProcessed;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeneratePlansAfterPurchase implements ShouldQueue
{
    public function handle(InitialPurchaseProcessed $event): void
    {
        $user = $event->user;
        $plan = $user->plans()->where('status', 'active')->latest()->first();

        if (! $plan) {
            Log::warning('No active plan found for initial generation after purchase', [
                'user_id' => $user->id,
            ]);

            return;
        }

        if ($plan->generation_completed_at) {
            return;
        }

        $workoutLock = "workout_plan_generation_{$plan->id}";
        $mealLock = "meal_plan_generation_{$plan->id}";

        if (Cache::add($workoutLock, true, now()->addMinutes(10))) {
            GenerateUserWorkoutPlan::dispatch($user, $plan);
        }

        if (Cache::add($mealLock, true, now()->addMinutes(10))) {
            GenerateUserMealPlan::dispatch($user, $plan);
        }

        Log::info('Triggered initial plan generation after purchase', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'product_id' => $event->event['product_id'],
        ]);
    }
}
