<?php

namespace App\Support\PlanEvaluator;

use Illuminate\Support\Collection;

/**
 * Scores a workout plan from extracted facts against evidence-based thresholds.
 * Deterministic on purpose: the same plan always gets the same rating, and every
 * rating is tied to a rule, not model opinion. Ranges reflect the hypertrophy
 * consensus — ~10-20 working sets/muscle/week, ≥2 sessions/muscle/week,
 * progressive overload, balanced coverage, and scheduled recovery.
 *
 * This returns data only (rating + context). Human-facing text lives in lang
 * files; the verdict prose is voiced by the agent from the same context.
 */
class WorkoutRubric
{
    /** @var list<string> */
    private const MAJOR_GROUPS = ['legs', 'back', 'chest', 'shoulders', 'arms', 'core'];

    private const SETS_MIN = 10;

    private const SETS_MAX = 20;

    private const SETS_JUNK_LOW = 6;

    private const SETS_EXCESSIVE = 25;

    private const MIN_FREQUENCY = 2;

    private const BALANCE_GOOD = 0.5;

    private const BALANCE_POOR = 0.34;

    /** @var array<string, float> weights, summing to 1.0 */
    private const WEIGHTS = ['coverage' => 0.22, 'volume' => 0.18, 'balance' => 0.12, 'intensity' => 0.11, 'progression' => 0.15, 'frequency' => 0.14, 'recovery' => 0.08];

    /**
     * @param  array<string, mixed>  $facts  output of PlanExtractionAgent
     * @return array{score: int, dimensions: list<Dimension>}
     */
    public function score(array $facts): array
    {
        $groups = collect($facts['muscle_groups'] ?? [])
            ->filter(fn (array $group) => in_array($group['group'] ?? null, self::MAJOR_GROUPS, true))
            ->keyBy('group');

        $dimensions = [
            $this->coverage($groups),
            $this->volume($groups),
            $this->balance($groups),
            $this->intensity((string) ($facts['intensity'] ?? 'unspecified')),
            $this->progression((bool) ($facts['has_progression'] ?? false)),
            $this->frequency($groups),
            $this->recovery((int) ($facts['days_per_week'] ?? 0)),
        ];

        return ['score' => $this->overallScore($dimensions), 'dimensions' => $dimensions];
    }

    /**
     * @param  Collection<string, array<string, mixed>>  $groups
     */
    private function coverage(Collection $groups): Dimension
    {
        $missing = array_values(array_diff(self::MAJOR_GROUPS, $groups->keys()->all()));

        return new Dimension('coverage', $this->rate(
            good: $missing === [],
            poor: count($missing) >= 2,
        ), ['missing' => $missing]);
    }

    /**
     * @param  Collection<string, array<string, mixed>>  $groups
     */
    private function volume(Collection $groups): Dimension
    {
        $low = $groups->filter($this->belowMinimum(...))->keys()->all();
        $high = $groups->filter($this->aboveMaximum(...))->keys()->all();
        $inRange = $groups->filter($this->withinRange(...))->count();
        $offTarget = count($low) + count($high);

        return new Dimension('volume', $this->rate(
            good: $groups->isNotEmpty() && ! $low && ! $high && $inRange >= $groups->count() / 2,
            poor: $groups->isEmpty() || $offTarget >= $groups->count() / 2,
        ), ['low' => $low, 'high' => $high]);
    }

    /**
     * How evenly volume is spread across the trained groups. A plan that hammers
     * chest but barely touches legs is imbalanced even if every group appears.
     *
     * @param  Collection<string, array<string, mixed>>  $groups
     */
    private function balance(Collection $groups): Dimension
    {
        if ($groups->count() < 2) {
            return new Dimension('balance', Rating::Poor, ['ratio' => 0.0]);
        }

        $sets = $groups->map(fn (array $group) => (int) $group['weekly_sets']);
        $max = (int) $sets->max();
        $ratio = $max > 0 ? (int) $sets->min() / $max : 0.0;

        return new Dimension('balance', $this->rate(
            good: $ratio >= self::BALANCE_GOOD,
            poor: $ratio < self::BALANCE_POOR,
        ), [
            'ratio' => round($ratio, 2),
            'weakest' => $groups->sortBy('weekly_sets')->keys()->first(),
        ]);
    }

    /**
     * Effort gates volume: light, submaximal sets are a weak stimulus no matter
     * how many there are. Unspecified stays neutral and the verdict flags it.
     */
    private function intensity(string $intensity): Dimension
    {
        $rating = match ($intensity) {
            'hard' => Rating::Good,
            'light' => Rating::Poor,
            default => Rating::Ok,
        };

        return new Dimension('intensity', $rating, ['level' => $intensity]);
    }

    private function progression(bool $hasProgression): Dimension
    {
        return new Dimension('progression', $this->rate(good: $hasProgression, poor: ! $hasProgression));
    }

    /**
     * @param  Collection<string, array<string, mixed>>  $groups
     */
    private function frequency(Collection $groups): Dimension
    {
        $once = $groups->filter(fn (array $group) => (int) $group['sessions_per_week'] < self::MIN_FREQUENCY)->keys()->all();

        return new Dimension('frequency', $this->rate(
            good: $groups->isNotEmpty() && ! $once,
            poor: $groups->isEmpty() || count($once) > 2,
        ), ['once' => $once]);
    }

    private function recovery(int $daysPerWeek): Dimension
    {
        return new Dimension('recovery', $this->rate(
            good: $daysPerWeek <= 6,
            poor: $daysPerWeek > 6,
        ), ['days_per_week' => $daysPerWeek]);
    }

    /**
     * @param  array<string, mixed>  $group
     */
    private function belowMinimum(array $group): bool
    {
        return (int) $group['weekly_sets'] < self::SETS_JUNK_LOW;
    }

    /**
     * @param  array<string, mixed>  $group
     */
    private function aboveMaximum(array $group): bool
    {
        return (int) $group['weekly_sets'] > self::SETS_EXCESSIVE;
    }

    /**
     * @param  array<string, mixed>  $group
     */
    private function withinRange(array $group): bool
    {
        return (int) $group['weekly_sets'] >= self::SETS_MIN && (int) $group['weekly_sets'] <= self::SETS_MAX;
    }

    private function rate(bool $good, bool $poor): Rating
    {
        return match (true) {
            $good => Rating::Good,
            $poor => Rating::Poor,
            default => Rating::Ok,
        };
    }

    /**
     * @param  list<Dimension>  $dimensions
     */
    private function overallScore(array $dimensions): int
    {
        $total = collect($dimensions)->sum(fn (Dimension $dimension) => $dimension->rating->points() * self::WEIGHTS[$dimension->key]);

        return (int) round($total * 100);
    }
}
