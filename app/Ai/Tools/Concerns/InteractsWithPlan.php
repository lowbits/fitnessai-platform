<?php

namespace App\Ai\Tools\Concerns;

use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;

/**
 * Shared plan lookups for tools that operate on the user's current plan.
 */
trait InteractsWithPlan
{
    protected function activePlan(User $user): ?Plan
    {
        return $user->plans()->where('status', 'active')->first();
    }

    /**
     * The MealPlan for today, with its meals loaded — or null if the user has
     * no active plan or today's day hasn't been generated.
     */
    protected function todaysMealPlan(User $user): ?MealPlan
    {
        $plan = $this->activePlan($user);

        if (! $plan) {
            return null;
        }

        $dayNumber = (int) $plan->start_date->diffInDays(today()) + 1;

        return MealPlan::with('meals')
            ->where('plan_id', $plan->id)
            ->where('day_number', $dayNumber)
            ->first();
    }
}
