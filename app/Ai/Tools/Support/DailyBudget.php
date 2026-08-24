<?php

namespace App\Ai\Tools\Support;

use App\Models\User;

/**
 * The user's calorie budget for today: their goal, what they have already
 * eaten (tracked), and what is still open. "Open calories" = goal - eaten.
 */
final class DailyBudget
{
    public function __construct(
        public readonly int $goal,
        public readonly float $eaten,
        public readonly float $remaining,
    ) {}

    public static function for(User $user): self
    {
        $goal = (int) ($user->plans()->where('status', 'active')->value('daily_calories') ?? 0);
        $eaten = (float) $user->calorieTrackings()->whereDate('tracked_date', today())->sum('calories');

        return new self($goal, $eaten, $goal - $eaten);
    }
}
