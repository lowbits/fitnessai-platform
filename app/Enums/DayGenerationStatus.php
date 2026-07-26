<?php

namespace App\Enums;

use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\WorkoutPlan;

/**
 * How far along a plan day's content is — the "generation" axis, independent of
 * whether the user may access it (see {@see DayAccess}).
 */
enum DayGenerationStatus: string
{
    case NotGenerated = 'not_generated';
    case Generating = 'generating';
    case Generated = 'generated';
    case Partial = 'partial';
    case Failed = 'failed';

    public static function determine(?MealPlan $mealPlan, ?WorkoutPlan $workoutPlan, Plan $plan, bool $isFirstDay): self
    {
        // Day 1 is dispatched at onboarding before records exist — infer generating.
        $awaiting = $isFirstDay && ! $plan->generation_completed_at;
        $meal = $mealPlan?->status ?? ($awaiting ? 'pending' : 'not_generated');
        $workout = $workoutPlan?->status ?? ($awaiting ? 'pending' : 'not_generated');

        return match (true) {
            $meal === 'pending' || $workout === 'pending' => self::Generating,
            $meal === 'failed' && $workout === 'failed' => self::Failed,
            $meal === 'failed' || $workout === 'failed' => self::Partial,
            $meal === 'generated' && $workout === 'generated' => self::Generated,
            $meal === 'generated' || $workout === 'generated' => self::Generating,
            default => self::NotGenerated,
        };
    }
}
