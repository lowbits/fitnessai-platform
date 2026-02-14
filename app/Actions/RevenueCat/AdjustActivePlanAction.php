<?php

namespace App\Actions\RevenueCat;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdjustActivePlanAction
{
    /**
     * Adjust the user's active plan duration and trigger generation if needed.
     */
    public function execute(User $user, string $productId, ?int $months = null): void
    {
        $activePlan = $user->plans()->where('status', 'active')->latest()->first();

        if (!$activePlan) {
            Log::warning('No active plan found for user to adjust', [
                'user_id' => $user->id,
            ]);
            return;
        }

        // Adjust duration based on product or manual months
        $startDate = $activePlan->start_date->copy();
        $endDate = $startDate->copy();

        if ($months !== null) {
            $endDate->addMonthsNoOverflow($months);
        } elseif (str_contains($productId, 'annual') || str_contains($productId, 'yearly')) {
            $endDate->addYearNoOverflow();
        } elseif (str_contains($productId, 'lifetime')) {
            $endDate->addYearsNoOverflow(100);
        } else {
            $endDate->addMonthNoOverflow();
        }

        $durationDays = (int) $startDate->diffInDays($endDate);

        $activePlan->update([
            'duration_days' => $durationDays,
            'end_date' => $endDate,
        ]);

        Log::info('Adjusted active plan duration', [
            'user_id' => $user->id,
            'plan_id' => $activePlan->id,
            'new_duration' => $durationDays,
            'product_id' => $productId,
        ]);

        // Check if user is behind generation week date
        if ($this->shouldTriggerGeneration($activePlan)) {
            GenerateUserWorkoutPlan::dispatch($user, $activePlan);
            GenerateUserMealPlan::dispatch($user, $activePlan);

            Log::info('Triggered next week generation after plan adjustment', [
                'user_id' => $user->id,
                'plan_id' => $activePlan->id,
            ]);
        }
    }

    private function shouldTriggerGeneration($plan): bool
    {
        // Get the latest workout plan date
        $lastWorkoutDate = $plan->workoutPlans()
            ->where('status', 'generated')
            ->max('date');

        if (!$lastWorkoutDate) {
            return true;
        }

        $lastDate = Carbon::parse($lastWorkoutDate);
        $today = now()->startOfDay();

        // If last generated date is less than 7 days in the future,
        // and we are already at or past the "generation day" (3 days after cycle start)
        // OR if the last generated date is very close (e.g. less than 2 days away)
        $daysInFuture = $today->diffInDays($lastDate, false);

        // Use similar logic as in GenerateWeeklyPlans
        $planStartDate = Carbon::parse($plan->start_date);
        $daysSinceStart = $planStartDate->diffInDays($today, false);
        $weekInCycle = (int) ($daysSinceStart / 7);
        $dayInWeek = $daysSinceStart % 7;

        // Generation day is day 3 of the week
        $isBehindGeneration = $dayInWeek >= 3 && $daysInFuture < 7;

        // Also trigger if we just don't have enough plans regardless of day of week
        $isRunningOut = $daysInFuture < 3;

        return $isBehindGeneration || $isRunningOut;
    }
}
