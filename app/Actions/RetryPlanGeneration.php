<?php

namespace App\Actions;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Plan;

class RetryPlanGeneration
{
    /**
     * Retry workout generation for a plan.
     * Resets any failed workout plans to pending and dispatches the generation job.
     */
    public static function workouts(Plan $plan): void
    {
        $plan->workoutPlans()->where('status', 'failed')->update(['status' => 'pending']);
        GenerateUserWorkoutPlan::dispatch($plan->user, $plan);
    }

    /**
     * Retry meal plan generation for a plan.
     * Resets any failed meal plans to pending and dispatches the generation job.
     */
    public static function meals(Plan $plan): void
    {
        $plan->mealPlans()->where('status', 'failed')->update(['status' => 'pending']);
        GenerateUserMealPlan::dispatch($plan->user, $plan);
    }
}
