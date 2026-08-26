<?php

namespace App\Ai\Tools\Support;

use App\Models\HealthDailyMetric;
use App\Models\User;

/**
 * The user's calorie budget for today: their goal, the activity credited back
 * from Apple Health, what they have already eaten (tracked), and what is still
 * open. "Open calories" = goal + credited - eaten.
 */
final class DailyBudget
{
    public function __construct(
        public readonly int $goal,
        public readonly float $eaten,
        public readonly float $remaining,
        public readonly int $credited = 0,
    ) {}

    public static function for(User $user): self
    {
        $goal = (int) ($user->plans()->where('status', 'active')->value('daily_calories') ?? 0);
        $eaten = (float) $user->calorieTrackings()->whereDate('tracked_date', today())->sum('calories');
        $credited = self::creditedFor($user);

        return new self($goal, $eaten, $goal + $credited - $eaten, $credited);
    }

    private static function creditedFor(User $user): int
    {
        if (! $user->activity_credit_enabled) {
            return 0;
        }

        return (int) (HealthDailyMetric::where('user_id', $user->id)
            ->whereDate('date', today())
            ->value('credited_kcal') ?? 0);
    }
}
