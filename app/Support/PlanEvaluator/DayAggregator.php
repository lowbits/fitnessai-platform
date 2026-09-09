<?php

namespace App\Support\PlanEvaluator;

/**
 * Turns the per-day extraction into weekly facts deterministically. The model
 * extracts each training day (which it can see directly); the weekly totals and
 * training frequency are summed here in code, where they are reliable. This is
 * what stops a four day plan from being read as "every muscle once a week".
 */
class DayAggregator
{
    /**
     * @param  array<string, mixed>  $facts
     * @return array<string, mixed>
     */
    public function aggregate(array $facts): array
    {
        $days = $facts['days'] ?? null;

        if (! is_array($days) || $days === []) {
            return $facts;
        }

        $weeklySets = [];
        $sessions = [];
        $daysPerWeek = 0;

        foreach ($days as $day) {
            $times = max(1, (int) ($day['times_per_week'] ?? 1));
            $daysPerWeek += $times;
            $trainedToday = [];

            foreach ($day['groups'] ?? [] as $entry) {
                $group = $entry['group'] ?? null;

                if (! is_string($group)) {
                    continue;
                }

                $weeklySets[$group] = ($weeklySets[$group] ?? 0) + (int) ($entry['sets'] ?? 0) * $times;
                $trainedToday[$group] = true;
            }

            foreach (array_keys($trainedToday) as $group) {
                $sessions[$group] = ($sessions[$group] ?? 0) + $times;
            }
        }

        $facts['muscle_groups'] = collect($weeklySets)
            ->map(fn (int $sets, string $group) => [
                'group' => $group,
                'weekly_sets' => $sets,
                'sessions_per_week' => $sessions[$group] ?? 0,
            ])
            ->values()
            ->all();

        $facts['days_per_week'] = $daysPerWeek;

        return $facts;
    }
}
