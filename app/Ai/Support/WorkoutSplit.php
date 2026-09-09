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
            2 => [
                ['label' => 'Upper Body', 'muscles' => 'chest, back, shoulders, biceps, triceps'],
                ['label' => 'Lower Body', 'muscles' => 'quadriceps, hamstrings, glutes, calves'],
            ],
            3 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs & Core', 'muscles' => 'quadriceps, hamstrings, glutes, calves, core'],
            ],
            4 => [
                ['label' => 'Upper Body A', 'muscles' => 'chest, back, shoulders'],
                ['label' => 'Lower Body A', 'muscles' => 'quadriceps, hamstrings, glutes'],
                ['label' => 'Upper Body B', 'muscles' => 'back, biceps, triceps, rear_delts'],
                ['label' => 'Lower Body B', 'muscles' => 'glutes, hamstrings, calves'],
            ],
            5 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'quadriceps, hamstrings, glutes, calves'],
                ['label' => 'Upper Body', 'muscles' => 'chest, back, shoulders'],
                ['label' => 'Lower Body', 'muscles' => 'glutes, hamstrings, calves'],
            ],
            6 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'quadriceps, hamstrings, glutes, calves'],
                ['label' => 'Push', 'muscles' => 'shoulders, chest, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'hamstrings, glutes, calves'],
            ],
            7 => [
                ['label' => 'Push', 'muscles' => 'chest, shoulders, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'quadriceps, hamstrings, glutes, calves'],
                ['label' => 'Push', 'muscles' => 'shoulders, chest, triceps'],
                ['label' => 'Pull', 'muscles' => 'back, biceps, rear_delts'],
                ['label' => 'Legs', 'muscles' => 'hamstrings, glutes, calves'],
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
