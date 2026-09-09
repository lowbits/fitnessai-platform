<?php

namespace App\Actions;

use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Plan;
use App\Models\User;
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
        $profile = $user->profile;
        $today = $this->todayDayNumber($plan);
        $mealDays = 0;
        $workoutDays = 0;

        DB::transaction(function () use ($plan, $profile, $today, &$mealDays, &$workoutDays) {
            $macros = $profile->getMetabolismData();
            $plan->update([
                'daily_calories' => $macros['daily_calories'],
                'daily_protein_g' => $macros['protein_g'],
                'daily_carbs_g' => $macros['carbs_g'],
                'daily_fat_g' => $macros['fat_g'],
                'generation_completed_at' => null,
            ]);

            $futureMeals = $plan->mealPlans()
                ->where('day_number', '>', $today)
                ->whereDoesntHave('meals', fn ($q) => $q->whereNotNull('completed_at'))
                ->get();

            foreach ($futureMeals as $mealPlan) {
                $mealPlan->meals()->delete();
                $mealPlan->update(['status' => 'pending']);
            }
            $mealDays = $futureMeals->count();

            $futureWorkouts = $plan->workoutPlans()
                ->where('day_number', '>', $today)
                ->whereDoesntHave('trackings')
                ->get();

            foreach ($futureWorkouts as $workoutPlan) {
                $workoutPlan->exercises()->delete();
                $workoutPlan->update(['status' => 'pending']);
            }
            $workoutDays = $futureWorkouts->count();
        });

        GenerateUserWorkoutPlan::dispatch($user, $plan);
        GenerateUserMealPlan::dispatch($user, $plan);

        Log::info('[PlanRegen] Dispatched', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'from_day' => $today + 1,
            'meal_days' => $mealDays,
            'workout_days' => $workoutDays,
        ]);

        return ['from_day' => $today + 1, 'meal_days' => $mealDays, 'workout_days' => $workoutDays];
    }

    private function todayDayNumber(Plan $plan): int
    {
        $start = CarbonImmutable::parse($plan->start_date)->startOfDay();
        $day = $start->diffInDays(CarbonImmutable::now()->startOfDay()) + 1;

        return max(1, min($day, $plan->duration_days));
    }
}
