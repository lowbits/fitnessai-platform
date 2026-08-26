<?php

namespace App\ValueObjects;

use App\Models\HealthDailyMetric;
use App\Models\User;

/**
 * The activity picture for a single day: whether Apple Health is connected,
 * whether the user lets it credit their budget, and — when a day has synced —
 * the measured energy, steps, workouts, and the credit that applies to the
 * budget. `credited` is already gated by the user's toggle, so callers can add
 * it to the budget without re-checking.
 */
final readonly class DayActivity
{
    public function __construct(
        public bool $connected,
        public bool $enabled,
        public ?int $measured,
        public ?int $steps,
        /** @var array<int, mixed> */
        public array $workouts,
        public int $credited,
        public bool $writebackEnabled,
    ) {}

    public static function build(User $user, ?HealthDailyMetric $metric): self
    {
        $enabled = (bool) $user->activity_credit_enabled;

        return new self(
            connected: $user->health_connected_at !== null,
            enabled: $enabled,
            measured: $metric?->active_energy_kcal,
            steps: $metric?->steps,
            workouts: $metric?->workouts ?? [],
            credited: $enabled && $metric ? $metric->credited_kcal : 0,
            writebackEnabled: (bool) $user->workout_writeback_enabled,
        );
    }
}
