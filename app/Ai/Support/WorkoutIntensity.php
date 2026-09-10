<?php

namespace App\Ai\Support;

use App\Enums\BodyGoal;

/**
 * The training intensity a plan should prescribe, derived from the body goal.
 * Kept deterministic so every generated exercise carries an effort target even
 * when the model omits one, which is what left plans reading as "unspecified".
 * Values mirror the RPE ranges in the goal protocol of CreateWorkoutPrompt.
 */
class WorkoutIntensity
{
    public static function defaultRpe(BodyGoal $goal): string
    {
        return match ($goal->resolveCanonical()) {
            BodyGoal::BUILD_MUSCLE => '7-9',
            default => '6-8',
        };
    }

    /**
     * Reduce whatever the model returns to a bare number or range ("8", "7-9"),
     * stripping any "RPE" prefix or "/10" suffix. Returns null when no valid
     * value on the 1-10 scale is present, so the caller falls back to the goal
     * default rather than storing nonsense like "12" or a reversed "9-7".
     */
    public static function normalizeRpe(?string $rpe): ?string
    {
        if ($rpe === null) {
            return null;
        }

        if (preg_match('/(\d{1,2})\s*-\s*(\d{1,2})/', $rpe, $match)) {
            $low = (int) $match[1];
            $high = (int) $match[2];

            return self::isValidRpe($low) && self::isValidRpe($high) && $low <= $high
                ? "{$low}-{$high}"
                : null;
        }

        if (preg_match('/\d{1,2}/', $rpe, $match)) {
            $value = (int) $match[0];

            return self::isValidRpe($value) ? (string) $value : null;
        }

        return null;
    }

    private static function isValidRpe(int $value): bool
    {
        return $value >= 1 && $value <= 10;
    }
}
