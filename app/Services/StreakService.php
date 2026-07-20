<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
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

        $activeDays = $this->activeDays($user);
        $streakDates = $this->currentStreakDates($activeDays);

        $perfectInStreak = $streakDates
            ->filter(fn (string $date) => $this->dayCompletion->for($user, $date, $plan)->isPerfect)
            ->count();
        $score = $streakDates->count() + $perfectInStreak * self::PERFECT_WEIGHT;

        return [
            'current' => $streakDates->count(),
            'longest' => $this->longestStreak($activeDays),
            'last_activity_on' => $activeDays->last(),
            'perfect_days' => $perfectInStreak,
            'score' => $score,
            'tier' => $this->tierFor($score),
            'next_tier' => $this->nextTier($score),
            'week' => $this->recentWeek($user, $plan),
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
     * Last seven days (oldest → today) with ring progress + perfect flag — the
     * same {@see DayCompletionService::progressOn} the dashboard week strip uses,
     * so both surfaces agree day-for-day.
     *
     * @return array<int, array{date: string, progress: float, complete: bool}>
     */
    private function recentWeek(User $user, ?Plan $plan): array
    {
        return collect(range(6, 0))
            ->map(function (int $daysAgo) use ($user, $plan) {
                $date = CarbonImmutable::today()->subDays($daysAgo)->toDateString();
                $entry = $plan ? $this->dayCompletion->progressOn($user, $plan, $date) : ['progress' => 0.0, 'complete' => false];

                return ['date' => $date] + $entry;
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
