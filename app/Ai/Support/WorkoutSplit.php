<?php

namespace App\Ai\Support;

use Illuminate\Support\Collection;

/**
 * Single source of truth for the weekly training split. The per-day focus, the
 * split name and muscle coverage all derive from one ordered list per weekly
 * frequency, so they can never drift apart (which is what once produced an
 * all-upper, no-legs four day plan).
 */
class WorkoutSplit
{
    /**
     * Ordered day focuses for a given number of training sessions per week.
     *
     * @return list<array{label: string, muscles: string}>
     */
    public static function forFrequency(int $workoutsPerWeek): array
    {
        return match ($workoutsPerWeek) {
            // Low frequency: full body. Every session targets all major muscles,
            // so each one is trained on every training day (2x/week at 2 days,
            // 3x at 3). Variation lives in exercise choice, never in omitting a
            // muscle from the target, which is what once left calves untrained.
            2 => [
                ['label' => 'Full Body A', 'muscles' => 'quadriceps, hamstrings, glutes, calves, chest, back, shoulders, biceps, triceps, core'],
                ['label' => 'Full Body B', 'muscles' => 'quadriceps, hamstrings, glutes, calves, chest, back, shoulders, biceps, triceps, core'],
            ],
            3 => [
                ['label' => 'Full Body A', 'muscles' => 'quadriceps, hamstrings, glutes, calves, chest, back, shoulders, biceps, triceps, core'],
                ['label' => 'Full Body B', 'muscles' => 'quadriceps, hamstrings, glutes, calves, chest, back, shoulders, biceps, triceps, core'],
                ['label' => 'Full Body C', 'muscles' => 'quadriceps, hamstrings, glutes, calves, chest, back, shoulders, biceps, triceps, core'],
            ],
            4 => [
                ['label' => 'Upper Body A', 'muscles' => 'chest, shoulders, triceps, back, biceps'],
                ['label' => 'Lower Body A', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
                ['label' => 'Upper Body B', 'muscles' => 'back, biceps, rear_delts, chest, shoulders, triceps'],
                ['label' => 'Lower Body B', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
            ],
            5 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs & Core', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
                ['label' => 'Upper Body', 'muscles' => 'chest, back, shoulders, biceps, triceps'],
                ['label' => 'Lower Body', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
            ],
            6 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs & Core', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
                ['label' => 'Push', 'muscles' => 'shoulders, chest, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs & Core', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
            ],
            7 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'quadriceps, hamstrings, glutes, calves'],
                ['label' => 'Push', 'muscles' => 'shoulders, chest, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'quadriceps, hamstrings, glutes, calves'],
                ['label' => 'Arms & Core', 'muscles' => 'biceps, triceps, core'],
            ],
            default => [
                ['label' => 'Full Body', 'muscles' => 'full_body'],
            ],
        };
    }

    /**
     * The focus for one session in the cycle (1-based), wrapping if needed.
     *
     * @return array{label: string, muscles: string}
     */
    public static function focus(int $workoutsPerWeek, int $workoutNumberInCycle): array
    {
        $split = self::forFrequency($workoutsPerWeek);

        return $split[($workoutNumberInCycle - 1) % count($split)];
    }

    public static function name(int $workoutsPerWeek): string
    {
        return (new Collection(self::forFrequency($workoutsPerWeek)))->pluck('label')->join(' / ');
    }
}
