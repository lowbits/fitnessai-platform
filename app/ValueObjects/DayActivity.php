<?php

namespace App\ValueObjects;

use App\Actions\Health\CompletedTrainingKcal;
use App\Models\HealthDailyMetric;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * The activity picture for a single day: whether Apple Health is connected,
 * whether the user lets it credit their budget, and — when a day has synced —
 * the measured energy, steps, workouts, and the credit that applies to the
 * budget. `credited` is already gated by the user's toggle, so callers can add
 * it to the budget without re-checking. The `activeEnergy`, `workoutEnergy` and
 * `trainingSubtracted` fields let the app show how the credit was reached.
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
        public ?int $activeEnergy,
        public ?int $workoutEnergy,
        public ?int $trainingSubtracted,
    ) {}

    public static function build(User $user, ?HealthDailyMetric $metric): self
    {
        $enabled = (bool) $user->activity_credit_enabled;

        // HealthKit keeps workout energy out of the active-energy total, so the true
        // active energy is the sum of both.
        $workoutKcal = collect($metric?->workouts ?? [])->sum(fn (array $w) => (int) ($w['energy_kcal'] ?? 0));

        $trainingSubtracted = $metric && $user->workout_writeback_enabled
            ? app(CompletedTrainingKcal::class)($user, Carbon::parse($metric->date)->toDateString())
            : ($metric ? 0 : null);

        return new self(
            connected: $user->health_connected_at !== null,
            enabled: $enabled,
            measured: $metric ? (int) $metric->active_energy_kcal + $workoutKcal : null,
            steps: $metric?->steps,
            workouts: $metric?->workouts ?? [],
            credited: $enabled && $metric ? $metric->credited_kcal : 0,
            writebackEnabled: (bool) $user->workout_writeback_enabled,
            activeEnergy: $metric ? (int) $metric->active_energy_kcal : null,
            workoutEnergy: $metric ? $workoutKcal : null,
            trainingSubtracted: $trainingSubtracted,
        );
    }
}
