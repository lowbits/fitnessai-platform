<?php

namespace App\Ai\Tools\Concerns;

use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;

/**
 * Shared plan lookups for tools that operate on the user's current plan.
 * Resolution is by the concrete calendar date, not a day-offset.
 */
trait InteractsWithPlan
{
    protected function activePlan(User $user): ?Plan
    {
        return $user->plans()->where('status', 'active')->first();
    }

    /**
     * Today's MealPlan (with meals) — or null if the user has no active plan or
     * today's day hasn't been generated.
     */
    protected function todaysMealPlan(User $user): ?MealPlan
    {
        $plan = $this->activePlan($user);

        if (! $plan) {
            return null;
        }

        return MealPlan::with('meals')
            ->where('plan_id', $plan->id)
            ->whereDate('date', today())
            ->first();
    }

    /** Today's WorkoutPlan (with exercises) — or null if none for today. */
    protected function todaysWorkoutPlan(User $user): ?WorkoutPlan
    {
        $plan = $this->activePlan($user);

        if (! $plan) {
            return null;
        }

        return WorkoutPlan::with('exercises.exercise')
            ->where('plan_id', $plan->id)
            ->whereDate('date', today())
            ->first();
    }

    /** The next actual training day after today (skipping rest days). */
    protected function nextWorkoutPlan(User $user): ?WorkoutPlan
    {
        $plan = $this->activePlan($user);

        if (! $plan) {
            return null;
        }

        return WorkoutPlan::with('exercises.exercise')
            ->where('plan_id', $plan->id)
            ->whereDate('date', '>', today())
            ->where('workout_type', '!=', 'rest')
            ->where('status', 'generated')
            ->orderBy('date')
            ->first();
    }
}
