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
     * stripping any "RPE" prefix or "/10" suffix. Returns null when no usable
     * number is present, so the caller can fall back to the goal default.
     */
    public static function normalizeRpe(?string $rpe): ?string
    {
        if ($rpe === null) {
            return null;
        }

        if (preg_match('/(\d{1,2})\s*-\s*(\d{1,2})/', $rpe, $match)) {
            return $match[1].'-'.$match[2];
        }

        if (preg_match('/\d{1,2}/', $rpe, $match)) {
            return $match[0];
        }

        return null;
    }
}
