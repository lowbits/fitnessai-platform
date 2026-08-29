<?php

namespace App\Actions\Plan;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Dispatch the workout + meal generation jobs for a user's plan. Idempotent:
 * skips an already-built plan and locks against a concurrent trigger, so it is
 * safe to call from both the pre-consent signup path and the consent grant.
 */
final class StartPlanGeneration
{
    public function execute(User $user, ?Plan $plan = null): void
    {
        $plan ??= $user->plans()->where('status', 'active')->latest()->first();

        if (! $plan || $plan->generation_completed_at) {
            return;
        }

        if (! Cache::add("plan_generation_{$plan->id}", true, now()->addMinutes(10))) {
            return;
        }

        GenerateUserWorkoutPlan::dispatch($user, $plan, maxDays: 1);
        GenerateUserMealPlan::dispatch($user, $plan, maxDays: 1);
        GenerateUserWorkoutPlan::dispatch($user, $plan);
        GenerateUserMealPlan::dispatch($user, $plan);
    }
}
