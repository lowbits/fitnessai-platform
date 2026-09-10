<?php

namespace App\Ai\Support;

use App\Enums\BodyGoal;

/**
 * RPE targets derived from the body goal, mirroring the goal protocol in
 * CreateWorkoutPrompt.
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
     * A bare number or range ("8", "7-9") on the 1-10 scale, or null if the
     * input holds no valid RPE.
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
