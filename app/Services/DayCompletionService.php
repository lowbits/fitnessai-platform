<?php

namespace App\Services;

use App\Models\Meal;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\ValueObjects\DayCompletion;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

/**
 * Owns the "perfect day" rule so the streak, the day endpoint and the week strip
 * share one definition: the calorie goal met (every recommended meal tracked, or
 * calories within tolerance of goal) AND — unless a rest day — the workout done.
 *
 * A past day never changes once it's over, so its facts are cached — a short TTL
 * bounds staleness in the rare case a past day is edited.
 */
final class DayCompletionService
{
    /** Tracked calories within this fraction below goal still meet the nutrition goal. */
    public const CALORIE_TOLERANCE = 0.05;

    /** Nutrition's share of a training day's ring; the workout fills the remainder. */
    public const TRAINING_NUTRITION_WEIGHT = 0.75;

    /**
     * The rule itself — pure, no database. Callers supply the day's facts.
     */
    public function evaluate(float $kcal, int $goal, bool $recommendedMet, bool $isTrainingDay, bool $workoutDone): DayCompletion
    {
        $nutritionMet = $goal > 0 && ($recommendedMet || $kcal >= $goal * (1 - self::CALORIE_TOLERANCE));
        $workoutOk = ! $isTrainingDay || $workoutDone;

        return new DayCompletion(
            nutritionMet: $nutritionMet,
            workoutDone: $workoutOk,
            isPerfect: $nutritionMet && $workoutOk,
        );
    }

    /**
     * Single day's perfect-day flags — used by the day endpoint, streak, milestones.
     */
    public function for(User $user, string $date, ?Plan $plan = null): DayCompletion
    {
        $plan ??= $user->plans()->where('status', 'active')->first();
        $goal = (int) ($plan?->daily_calories ?? 0);

        if (! $plan || $goal <= 0) {
            return $this->evaluate(0, 0, false, false, false);
        }

        $f = $this->dayFacts($user, $plan, $date);

        return $this->evaluate($f['kcal'], $goal, $f['recommendedMet'], $f['isTraining'], $f['workoutDone']);
    }

    /**
     * Ring progress (0..1) and perfect-day flag for one day — the pair the strip renders.
     *
     * @return array{progress: float, complete: bool}
     */
    public function progressOn(User $user, Plan $plan, string $date): array
    {
        $goal = (int) ($plan->daily_calories ?? 0);
        if ($goal <= 0) {
            return ['progress' => 0.0, 'complete' => false];
        }

        $f = $this->dayFacts($user, $plan, $date);
        $ratio = min(1.0, $f['kcal'] / $goal);

        // Nutrition fills a rest day's ring; a training day splits it 75/25 with the workout.
        $progress = $f['isTraining']
            ? self::TRAINING_NUTRITION_WEIGHT * $ratio + ($f['workoutDone'] ? 1 - self::TRAINING_NUTRITION_WEIGHT : 0.0)
            : $ratio;

        return [
            'progress' => round($progress, 4),
            'complete' => $this->evaluate($f['kcal'], $goal, $f['recommendedMet'], $f['isTraining'], $f['workoutDone'])->isPerfect,
        ];
    }

    /**
     * Week-strip entries for the days around $centerDate, clamped to the plan window
     * and today (future days are omitted — nothing tracked yet).
     *
     * @return list<array{date: string, progress: float, complete: bool}>
     */
    public function strip(User $user, Plan $plan, string $centerDate, int $radius = 7): array
    {
        $start = CarbonImmutable::parse($plan->start_date->toDateString());
        $center = CarbonImmutable::parse($centerDate);
        $from = $center->subDays($radius)->max($start);
        $to = $center->addDays($radius)
            ->min(CarbonImmutable::now())
            ->min($start->addDays(max(0, (int) $plan->duration_days - 1)));

        $strip = [];
        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            $date = $day->toDateString();
            $strip[] = ['date' => $date] + $this->progressOn($user, $plan, $date);
        }

        return $strip;
    }

    /**
     * The four facts the rule needs. Past days are cached (they don't change once
     * over); today and future are always computed live.
     *
     * @return array{kcal: float, recommendedMet: bool, isTraining: bool, workoutDone: bool}
     */
    private function dayFacts(User $user, Plan $plan, string $date): array
    {
        $day = CarbonImmutable::parse($date);
        $compute = fn (): array => [
            'kcal' => $this->caloriesOn($user, $date),
            'recommendedMet' => $this->recommendedMealsTracked($user, $plan, $date),
            'isTraining' => $this->isTrainingDay($plan, $date),
            'workoutDone' => $this->workoutDone($user, $date),
        ];

        if ($day->greaterThanOrEqualTo(CarbonImmutable::today())) {
            return $compute();
        }

        return Cache::remember("day_facts:{$user->id}:{$day->toDateString()}", now()->addDay(), $compute);
    }

    /** Total calories the user tracked on a day. */
    private function caloriesOn(User $user, string $date): float
    {
        return (float) $user->calorieTrackings()->whereDate('tracked_date', $date)->sum('calories');
    }

    /** A day with a planned workout that isn't a rest day. */
    private function isTrainingDay(Plan $plan, string $date): bool
    {
        $dayNumber = (int) $plan->start_date->diffInDays(CarbonImmutable::parse($date)) + 1;

        return WorkoutPlan::where('plan_id', $plan->id)
            ->where('day_number', $dayNumber)
            ->where('workout_type', '!=', 'rest')
            ->exists();
    }

    /** Whether the user completed a workout on a day. */
    private function workoutDone(User $user, string $date): bool
    {
        return $user->workoutTrackings()
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $date)
            ->exists();
    }

    /** Whether every recommended meal planned for the day was tracked. */
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
