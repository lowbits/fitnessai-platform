<?php

namespace App\Actions\Health;

use App\Models\User;

/**
 * Total estimated energy of a user's completed fytrr workouts on a date — the
 * training already priced into the daily goal. Uses the stored plan estimate,
 * falling back to a MET estimate from the session length and the user's weight.
 */
final class CompletedTrainingKcal
{
    public function __construct(private readonly EstimateWorkoutEnergy $estimate) {}

    public function __invoke(User $user, string $date): int
    {
        $weightKg = $user->getCurrentWeight();

        return (int) $user->workoutTrackings()
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $date)
            ->with('workoutPlan:id,workout_type,estimated_duration_minutes,estimated_calories_burned')
            ->get()
            ->sum(function ($tracking) use ($weightKg): int {
                $plan = $tracking->workoutPlan;

                if (! $plan) {
                    return 0;
                }

                return (int) ($plan->estimated_calories_burned
                    ?? ($this->estimate)($plan->workout_type, (int) $plan->estimated_duration_minutes, $weightKg)
                    ?? 0);
            });
    }
}
