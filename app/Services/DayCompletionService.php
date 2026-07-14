<?php

namespace App\Services;

use App\Models\Meal;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\ValueObjects\DayCompletion;
use Carbon\CarbonImmutable;

/**
 * Owns the "perfect day" rule so the streak, the day endpoint and any milestone
 * logic share one definition: the calorie goal met (every recommended meal
 * tracked, or calories within tolerance of goal) AND — unless a rest day — the
 * workout done.
 */
final class DayCompletionService
{
    /** Tracked calories within this fraction below goal still meet the nutrition goal. */
    public const CALORIE_TOLERANCE = 0.05;

    /**
     * The rule itself — pure, no database. Callers supply the day's facts.
     */
    public function evaluate(
        float $kcal,
        int $goal,
        bool $recommendedMet,
        bool $isTrainingDay,
        bool $workoutDone,
    ): DayCompletion {
        $nutritionMet = $goal > 0 && ($recommendedMet || $kcal >= $goal * (1 - self::CALORIE_TOLERANCE));
        $workoutOk = ! $isTrainingDay || $workoutDone;

        return new DayCompletion(
            nutritionMet: $nutritionMet,
            workoutDone: $workoutOk,
            isPerfect: $nutritionMet && $workoutOk,
        );
    }

    /**
     * Fetch a single day's facts and evaluate — used by the day endpoint.
     */
    public function for(User $user, string $date, ?Plan $plan = null): DayCompletion
    {
        $day = CarbonImmutable::parse($date)->toDateString();
        $plan ??= $user->plans()->where('status', 'active')->first();
        $goal = (int) ($plan?->daily_calories ?? 0);

        if (! $plan || $goal <= 0) {
            return $this->evaluate(0, 0, false, false, false);
        }

        $kcal = (float) $user->calorieTrackings()->whereDate('tracked_date', $day)->sum('calories');
        $recommendedMet = $this->recommendedMealsTracked($user, $plan, $day);

        $dayNumber = (int) $plan->start_date->diffInDays(CarbonImmutable::parse($day)) + 1;
        $isTrainingDay = WorkoutPlan::where('plan_id', $plan->id)->where('day_number', $dayNumber)->exists();
        $workoutDone = $user->workoutTrackings()
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $day)
            ->exists();

        return $this->evaluate($kcal, $goal, $recommendedMet, $isTrainingDay, $workoutDone);
    }

    /**
     * Whether every recommended meal planned for the day was tracked.
     */
    private function recommendedMealsTracked(User $user, Plan $plan, string $date): bool
    {
        $planned = Meal::query()
            ->join('meal_plans', 'meals.meal_plan_id', '=', 'meal_plans.id')
            ->where('meal_plans.plan_id', $plan->id)
            ->whereDate('meal_plans.date', $date)
            ->pluck('meals.id')
            ->unique();

        if ($planned->isEmpty()) {
            return false;
        }

        $tracked = $user->calorieTrackings()
            ->whereNotNull('meal_id')
            ->whereDate('tracked_date', $date)
            ->pluck('meal_id')
            ->unique();

        return $planned->diff($tracked)->isEmpty();
    }
}
