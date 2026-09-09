<?php

namespace App\Support\PlanEvaluator;

use Illuminate\Support\Collection;

/**
 * Builds the prompt for the verdict agent from a scored evaluation. The agent
 * only voices these findings — it never re-scores — so the prompt hands it the
 * localized label + evidence per dimension plus the raw context (which groups
 * are missing, over- or under-trained) for it to phrase in the user's language.
 */
class VerdictPrompt
{
    /**
     * @param  array{score: int, dimensions: list<Dimension>, facts?: array<string, mixed>}  $evaluation
     */
    public static function for(array $evaluation, string $tone, string $locale, string $context = ''): string
    {
        $findings = collect($evaluation['dimensions'])
            ->map(fn (Dimension $dimension) => self::line($dimension, $locale))
            ->implode("\n");

        $lines = [
            "Tone: {$tone}",
            "Language: {$locale}",
            "Overall score: {$evaluation['score']}/100",
            'Findings (voice these, do not add others):',
            $findings,
        ];

        $facts = $evaluation['facts'] ?? [];

        if (! empty($facts['days_per_week'])) {
            $split = trim((string) ($facts['split'] ?? ''));
            $lines[] = '';
            $lines[] = trim("Structure: {$split} across {$facts['days_per_week']} training days per week.");
            $lines[] = 'If the weekly frequency is low for the days available, suggest the fitting structure (for example an alternating full-body split at two or three days a week) so each muscle is trained about twice.';
        }

        $intensity = $facts['intensity'] ?? 'unspecified';
        if ($intensity === 'unspecified') {
            $lines[] = 'Intensity is not stated in the plan; note you cannot fully judge effort, and it may be limiting results.';
        } elseif ($intensity === 'light') {
            $lines[] = 'Intensity is light and submaximal; say plainly that the sets are too easy to grow well, and to train closer to failure.';
        }

        $measured = self::measured($facts);
        if ($measured !== '') {
            $lines[] = '';
            $lines[] = "Measured per muscle group (state only these numbers, never claim a group is trained less often or less than this): {$measured}";
        }

        if (trim($context) !== '') {
            $lines[] = '';
            $lines[] = "What the lifter told you: \"{$context}\". Speak to their goal, experience and constraints.";
        }

        $lines[] = '';
        $lines[] = 'Write the verdict now.';

        return implode("\n", $lines);
    }

    private static function line(Dimension $dimension, string $locale): string
    {
        $label = trans("plan_roast.dimensions.{$dimension->key}.label", [], $locale);
        $evidence = trans("plan_roast.dimensions.{$dimension->key}.evidence", [], $locale);
        $details = self::details($dimension);

        return "- {$label} [{$dimension->rating->value}]: {$evidence}".($details === '' ? '' : " (details: {$details})");
    }

    /**
     * @param  array<string, mixed>  $facts
     */
    private static function measured(array $facts): string
    {
        return collect($facts['muscle_groups'] ?? [])
            ->map(fn (array $group) => "{$group['group']} {$group['weekly_sets']} sets across {$group['sessions_per_week']}x/week")
            ->implode('; ');
    }

    private static function details(Dimension $dimension): string
    {
        return (new Collection($dimension->context))
            ->reject(fn (mixed $value) => $value === [] || $value === null || $value === '')
            ->map(fn (mixed $value, string $key) => $key.': '.(is_array($value) ? implode(', ', $value) : $value))
            ->implode('; ');
    }
}
