<?php

namespace App\Actions;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Rebuilds the remaining days of an active plan so it reflects the user's
 * current profile (goal, calories, focus areas, limitations). Past days and
 * anything already eaten or trained are preserved — only future, untouched
 * days are reset and re-dispatched to the existing generation jobs.
 */
class RegenerateRemainingPlan
{
    /**
     * @return array{from_day: int, meal_days: int, workout_days: int}
     */
    public function execute(User $user, Plan $plan): array
    {
        $today = $this->todayDayNumber($plan);

        [$mealDays, $workoutDays] = DB::transaction(function () use ($plan, $user, $today) {
            $this->refreshTargets($plan, $user->profile);

            return [
                $this->resetFutureMealDays($plan, $today),
                $this->resetFutureWorkoutDays($plan, $today),
            ];
        });

        $resetDays = [...$mealDays, ...$workoutDays];

        if ($resetDays !== []) {
            $window = max($resetDays) - $today;
            GenerateUserWorkoutPlan::dispatch($user, $plan, $window);
            GenerateUserMealPlan::dispatch($user, $plan, $window);
        }

        Log::info('[PlanRegen] Dispatched', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'from_day' => $today + 1,
            'meal_days' => count($mealDays),
            'workout_days' => count($workoutDays),
        ]);

        return ['from_day' => $today + 1, 'meal_days' => count($mealDays), 'workout_days' => count($workoutDays)];
    }

    private function refreshTargets(Plan $plan, UserProfile $profile): void
    {
        $macros = $profile->getMetabolismData();

        $plan->update([
            'daily_calories' => $macros['daily_calories'],
            'daily_protein_g' => $macros['protein_g'],
            'daily_carbs_g' => $macros['carbs_g'],
            'daily_fat_g' => $macros['fat_g'],
            'generation_completed_at' => null,
        ]);
    }

    /**
     * @return list<int> day numbers reset (future days with nothing eaten yet)
     */
    private function resetFutureMealDays(Plan $plan, int $today): array
    {
        $mealPlans = $plan->mealPlans()
            ->where('day_number', '>', $today)
            ->whereDoesntHave('meals', fn ($query) => $query->whereNotNull('completed_at'))
            ->get();

        foreach ($mealPlans as $mealPlan) {
            $mealPlan->meals()->delete();
            $mealPlan->update(['status' => 'pending']);
        }

        return $mealPlans->pluck('day_number')->all();
    }

    /**
     * @return list<int> day numbers reset (future days with no logged training)
     */
    private function resetFutureWorkoutDays(Plan $plan, int $today): array
    {
        $workoutPlans = $plan->workoutPlans()
            ->where('day_number', '>', $today)
            ->whereDoesntHave('trackings')
            ->get();

        foreach ($workoutPlans as $workoutPlan) {
            $workoutPlan->exercises()->delete();
            $workoutPlan->update(['status' => 'pending']);
        }

        return $workoutPlans->pluck('day_number')->all();
    }

    private function todayDayNumber(Plan $plan): int
    {
        $start = CarbonImmutable::parse($plan->start_date)->startOfDay();
        $day = $start->diffInDays(CarbonImmutable::now()->startOfDay()) + 1;

        return max(1, min($day, $plan->duration_days));
    }
}
