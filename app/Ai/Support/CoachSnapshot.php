<?php

namespace App\Ai\Support;

use App\Ai\Tools\Support\DailyBudget;
use App\Models\User;
use App\Services\StreakService;

/**
 * A compact, live picture of where the user actually is right now — weight
 * trend, streak, plan progress, today's calories, last check-in. Folded into
 * Mona's instructions so every reply is grounded in this person's journey
 * instead of generic advice. The single source for "what Mona knows about you".
 */
final class CoachSnapshot
{
    public function __construct(private readonly StreakService $streaks) {}

    public function forUser(User $user): string
    {
        return implode(' ', array_filter([
            $this->weight($user),
            $this->streak($user),
            $this->plan($user),
            $this->today($user),
            $this->lastCheckIn($user),
        ]));
    }

    private function weight(User $user): string
    {
        $start = $user->profile?->weight_kg
            ?? $user->bodyProgress()->orderBy('recorded_at')->value('weight_kg');
        $current = $user->bodyProgress()->orderByDesc('recorded_at')->value('weight_kg') ?? $start;

        if ($current === null) {
            return '';
        }

        $line = "Weight: {$current} kg";

        if ($start !== null && (float) $start !== (float) $current) {
            $delta = round((float) $current - (float) $start, 1);
            $line .= ' ('.($delta > 0 ? '+' : '')."{$delta} kg since start)";
        }

        return $line.'.';
    }

    private function streak(User $user): string
    {
        $streak = $this->streaks->for($user);

        if (($streak['current'] ?? 0) <= 0) {
            return '';
        }

        return "Streak: {$streak['current']} days, tier {$streak['tier']}.";
    }

    private function plan(User $user): string
    {
        $plan = $user->plans()->where('status', 'active')->first();

        if (! $plan?->start_date) {
            return '';
        }

        $day = min(max((int) $plan->start_date->diffInDays(today()) + 1, 1), (int) $plan->duration_days);

        return "Day {$day} of a {$plan->duration_days}-day plan.";
    }

    private function today(User $user): string
    {
        $budget = DailyBudget::for($user);

        if ($budget->goal <= 0) {
            return '';
        }

        return 'Today: '.round($budget->eaten).' of '.$budget->goal.' kcal eaten, '
            .round($budget->remaining).' kcal left.';
    }

    private function lastCheckIn(User $user): string
    {
        $checkIn = $user->checkIns()->latest('checked_in_at')->first();

        if (! $checkIn || $checkIn->mood === null) {
            return '';
        }

        return "Last check-in mood {$checkIn->mood}/5, energy {$checkIn->energy}/5.";
    }
}
