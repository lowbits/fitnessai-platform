<?php

namespace App\Services;

use App\Models\CalorieTracking;
use App\Models\Meal;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Activity streak + weighted score, computed on read from tracking records —
 * no stored state, always consistent with the underlying data.
 *
 * A day is ACTIVE (counts toward the streak) when the user tracked a meal or
 * completed a workout that day. A day is PERFECT when {@see DayCompletionService}
 * says so — that service owns the rule; this one only tallies streaks from it.
 *
 * The score weights quality over bare consistency (a 30-day streak with 20
 * perfect days outranks a 30-day streak with none) and drives the card tier.
 * It resets when the streak breaks.
 */
class StreakService
{
    public function __construct(private readonly DayCompletionService $dayCompletion) {}

    private const PERFECT_WEIGHT = 2;

    /** Descending — first match wins. */
    private const TIERS = [
        ['tier' => 'platinum', 'min' => 75],
        ['tier' => 'gold', 'min' => 30],
        ['tier' => 'silver', 'min' => 10],
        ['tier' => 'bronze', 'min' => 3],
        ['tier' => 'spark', 'min' => 1],
    ];

    /**
     * @return array{
     *     current: int, longest: int, last_activity_on: ?string,
     *     perfect_days: int, score: int, tier: string,
     *     next_tier: ?array{tier: string, score_needed: int},
     *     week: array<int, array{date: string, progress: float, complete: bool}>
     * }
     */
    public function for(User $user): array
    {
        $plan = $user->plans()->where('status', 'active')->first();
        $goal = (int) ($plan?->daily_calories ?? 0);

        $activeDays = $this->activeDays($user);
        $kcalByDay = $this->kcalByDay($user);
        $perfectDays = $this->perfectDays($user, $plan, $goal, $kcalByDay)->flip();

        $streakDates = $this->currentStreakDates($activeDays);
        $perfectInStreak = $streakDates->filter(fn (string $date) => $perfectDays->has($date))->count();
        $score = $streakDates->count() + $perfectInStreak * self::PERFECT_WEIGHT;

        return [
            'current' => $streakDates->count(),
            'longest' => $this->longestStreak($activeDays),
            'last_activity_on' => $activeDays->last(),
            'perfect_days' => $perfectInStreak,
            'score' => $score,
            'tier' => $this->tierFor($score),
            'next_tier' => $this->nextTier($score),
            'week' => $this->recentWeek($perfectDays, $kcalByDay, $goal),
        ];
    }

    /**
     * Distinct calendar days (ascending, `Y-m-d`) the user logged food or
     * completed a workout.
     *
     * @return Collection<int, string>
     */
    private function activeDays(User $user): Collection
    {
        $foodDays = $user->calorieTrackings()->pluck('tracked_date');

        $workoutDays = $user->workoutTrackings()
            ->whereNotNull('completed_at')
            ->pluck('completed_at');

        return $this->toDayKeys($foodDays->merge($workoutDays));
    }

    /**
     * Total tracked calories per calendar day, keyed by `Y-m-d`.
     *
     * @return Collection<string, float>
     */
    private function kcalByDay(User $user): Collection
    {
        return $user->calorieTrackings()
            ->selectRaw('tracked_date, SUM(calories) as total')
            ->groupBy('tracked_date')
            ->pluck('total', 'tracked_date')
            ->mapWithKeys(fn ($total, $date) => [CarbonImmutable::parse($date)->toDateString() => (float) $total]);
    }

    /**
     * Days (`Y-m-d`) that met the nutrition goal and — unless a rest day — the workout.
     *
     * @param  Collection<string, float>  $kcalByDay
     * @return Collection<int, string>
     */
    private function perfectDays(User $user, ?Plan $plan, int $goal, Collection $kcalByDay): Collection
    {
        if (! $plan || $goal <= 0) {
            return collect();
        }

        $recommendedComplete = $this->recommendedMealDays($user, $plan)->flip();

        $workoutDone = $this->toDayKeys(
            $user->workoutTrackings()->whereNotNull('completed_at')->pluck('completed_at')
        )->flip();

        $trainingDayNumbers = WorkoutPlan::where('plan_id', $plan->id)->pluck('day_number')->flip();

        return $kcalByDay
            ->filter(function (float $total, string $date) use ($goal, $recommendedComplete, $trainingDayNumbers, $workoutDone, $plan) {
                $dayNumber = (int) $plan->start_date->diffInDays(CarbonImmutable::parse($date)) + 1;

                return $this->dayCompletion->evaluate(
                    kcal: $total,
                    goal: $goal,
                    recommendedMet: $recommendedComplete->has($date),
                    isTrainingDay: $trainingDayNumbers->has($dayNumber),
                    workoutDone: $workoutDone->has($date),
                )->isPerfect;
            })
            ->keys()
            ->values();
    }

    /**
     * Dates (`Y-m-d`) on which every recommended meal for the day was tracked.
     *
     * @return Collection<int, string>
     */
    private function recommendedMealDays(User $user, Plan $plan): Collection
    {
        $plannedByDate = Meal::query()
            ->join('meal_plans', 'meals.meal_plan_id', '=', 'meal_plans.id')
            ->where('meal_plans.plan_id', $plan->id)
            ->get(['meals.id', 'meal_plans.date'])
            ->groupBy(fn (Meal $meal) => CarbonImmutable::parse($meal->date)->toDateString());

        if ($plannedByDate->isEmpty()) {
            return collect();
        }

        $trackedByDate = $user->calorieTrackings()
            ->whereNotNull('meal_id')
            ->get(['meal_id', 'tracked_date'])
            ->groupBy(fn (CalorieTracking $tracking) => CarbonImmutable::parse($tracking->tracked_date)->toDateString());

        return $plannedByDate
            ->filter(function (Collection $meals, string $date) use ($trackedByDate) {
                $planned = $meals->pluck('id')->unique();
                $tracked = ($trackedByDate->get($date) ?? collect())->pluck('meal_id')->unique();

                return $planned->isNotEmpty() && $planned->diff($tracked)->isEmpty();
            })
            ->keys()
            ->values();
    }

    /**
     * Dates of the current unbroken streak. Grace: an in-progress today with no
     * activity yet counts from yesterday.
     *
     * @param  Collection<int, string>  $activeDays  ascending, distinct
     * @return Collection<int, string>
     */
    private function currentStreakDates(Collection $activeDays): Collection
    {
        $tracked = $activeDays->flip();

        $cursor = CarbonImmutable::today();
        if (! $tracked->has($cursor->toDateString()) && $tracked->has($cursor->subDay()->toDateString())) {
            $cursor = $cursor->subDay();
        }

        $dates = collect();
        while ($tracked->has($cursor->toDateString())) {
            $dates->push($cursor->toDateString());
            $cursor = $cursor->subDay();
        }

        return $dates;
    }

    /**
     * @param  Collection<int, string>  $activeDays  ascending, distinct
     */
    private function longestStreak(Collection $activeDays): int
    {
        $longest = 0;
        $run = 0;
        $previous = null;

        foreach ($activeDays as $day) {
            $date = CarbonImmutable::parse($day);
            $run = $previous?->addDay()->isSameDay($date) ? $run + 1 : 1;
            $longest = max($longest, $run);
            $previous = $date;
        }

        return $longest;
    }

    /**
     * Last seven days (oldest → today) with calorie progress and a perfect flag.
     *
     * @param  Collection<string, int>  $perfectDays  flipped set
     * @param  Collection<string, float>  $kcalByDay
     * @return array<int, array{date: string, progress: float, complete: bool}>
     */
    private function recentWeek(Collection $perfectDays, Collection $kcalByDay, int $goal): array
    {
        return collect(range(6, 0))
            ->map(function (int $daysAgo) use ($perfectDays, $kcalByDay, $goal) {
                $date = CarbonImmutable::today()->subDays($daysAgo)->toDateString();
                $progress = $goal > 0 ? min(1.0, round($kcalByDay->get($date, 0.0) / $goal, 3)) : 0.0;

                return ['date' => $date, 'progress' => $progress, 'complete' => $perfectDays->has($date)];
            })
            ->all();
    }

    private function tierFor(int $score): string
    {
        foreach (self::TIERS as $tier) {
            if ($score >= $tier['min']) {
                return $tier['tier'];
            }
        }

        return 'none';
    }

    /**
     * @return ?array{tier: string, score_needed: int}
     */
    private function nextTier(int $score): ?array
    {
        foreach (array_reverse(self::TIERS) as $tier) {
            if ($score < $tier['min']) {
                return ['tier' => $tier['tier'], 'score_needed' => $tier['min']];
            }
        }

        return null;
    }

    /**
     * Timestamps → distinct ascending `Y-m-d` day keys.
     *
     * @param  Collection<int, mixed>  $timestamps
     * @return Collection<int, string>
     */
    private function toDayKeys(Collection $timestamps): Collection
    {
        return $timestamps
            ->map(fn ($timestamp) => CarbonImmutable::parse($timestamp)->toDateString())
            ->unique()
            ->sort()
            ->values();
    }
}
