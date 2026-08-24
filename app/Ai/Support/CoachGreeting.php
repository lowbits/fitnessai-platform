<?php

namespace App\Ai\Support;

use App\Models\User;
use App\Services\StreakService;

/**
 * The proactive opener Mona shows when the user opens the coach. Deterministic
 * and localized (no model call) so the coach opens instantly, but personal:
 * it reacts to the strongest signal in the user's data right now.
 */
final class CoachGreeting
{
    public function __construct(private readonly StreakService $streaks) {}

    /**
     * @return array{key: string, params: array<string, mixed>}
     */
    public function for(User $user): array
    {
        $name = trim((string) ($user->name ?? ''));
        $first = $name === '' ? '' : explode(' ', $name)[0];

        $start = $user->profile?->weight_kg
            ?? $user->bodyProgress()->orderBy('recorded_at')->value('weight_kg');
        $current = $user->bodyProgress()->orderByDesc('recorded_at')->value('weight_kg');
        $streak = (int) ($this->streaks->for($user)['current'] ?? 0);
        $lastCheckIn = $user->checkIns()->latest('checked_in_at')->value('checked_in_at');
        $hasData = $current !== null || $streak > 0;

        if (! $hasData) {
            return ['key' => 'coach.opener.welcome', 'params' => ['name' => $first]];
        }

        if ($start !== null && $current !== null && (float) $current < (float) $start - 0.3) {
            $lost = round((float) $start - (float) $current, 1);

            return ['key' => 'coach.opener.weight_down', 'params' => ['name' => $first, 'kg' => $lost]];
        }

        if ($streak >= 3) {
            return ['key' => 'coach.opener.streak', 'params' => ['name' => $first, 'days' => $streak]];
        }

        if ($lastCheckIn === null || $lastCheckIn->diffInDays(now()) >= 7) {
            return ['key' => 'coach.opener.check_in', 'params' => ['name' => $first]];
        }

        return ['key' => 'coach.opener.default', 'params' => ['name' => $first]];
    }

    public function textFor(User $user): string
    {
        $greeting = $this->for($user);

        return trim((string) trans($greeting['key'], $greeting['params']));
    }
}
