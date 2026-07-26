<?php

namespace App\Services;

use App\Enums\DayGenerationStatus;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Carbon\CarbonImmutable;

/**
 * A light, pollable summary of the seven-day plan week containing a date: per-day
 * generation status + thumbnails + workout meta. Drives the plan-reveal screen and
 * a week overview; the day endpoint stays the source for a single day's detail.
 */
final class PlanWeekService
{
    private const MEAL_TYPE_ORDER = ['breakfast' => 1, 'lunch' => 2, 'snack' => 3, 'dinner' => 4];

    /**
     * @return array<string, mixed>
     */
    public function build(User $user, Plan $plan, CarbonImmutable $anchor): array
    {
        $start = CarbonImmutable::parse($plan->start_date->toDateString());
        $dayOffset = max(0, (int) $start->diffInDays($anchor));
        $weekStart = $start->addDays(intdiv($dayOffset, 7) * 7);
        $weekEnd = $weekStart->addDays(7);

        $meals = MealPlan::with('meals')
            ->where('plan_id', $plan->id)
            ->where('date', '>=', $weekStart->toDateString())
            ->where('date', '<', $weekEnd->toDateString())
            ->get()
            ->keyBy(fn (MealPlan $meal) => $meal->date->toDateString());

        $workouts = WorkoutPlan::where('plan_id', $plan->id)
            ->where('date', '>=', $weekStart->toDateString())
            ->where('date', '<', $weekEnd->toDateString())
            ->get()
            ->keyBy(fn (WorkoutPlan $workout) => $workout->date->toDateString());

        $days = collect(range(0, 6))->map(function (int $offset) use ($weekStart, $start, $meals, $workouts, $plan) {
            $date = $weekStart->addDays($offset);
            $mealPlan = $meals->get($date->toDateString());
            $workoutPlan = $workouts->get($date->toDateString());

            return [
                'date' => $date->toDateString(),
                'weekday' => strtolower($date->format('l')),
                'status' => DayGenerationStatus::determine($mealPlan, $workoutPlan, $plan, $date->equalTo($start))->value,
                'kcal' => (int) ($mealPlan?->total_calories ?? $plan->daily_calories),
                'meals' => $this->mealThumbnails($mealPlan),
                'workout' => $this->workoutSummary($workoutPlan),
            ];
        });

        return [
            'week_start' => $weekStart->toDateString(),
            'ready' => ($days->first()['status'] ?? null) === DayGenerationStatus::Generated->value,
            'days_generated' => $days->where('status', DayGenerationStatus::Generated->value)->count(),
            'days' => $days->all(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mealThumbnails(?MealPlan $mealPlan): array
    {
        if (! $mealPlan) {
            return [];
        }

        return $mealPlan->meals
            ->sortBy(fn ($meal) => self::MEAL_TYPE_ORDER[strtolower($meal->type)] ?? 99)
            ->map(fn ($meal) => [
                'type' => strtolower($meal->type),
                'thumbnail_url' => $meal->thumbnail_url,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function workoutSummary(?WorkoutPlan $workoutPlan): ?array
    {
        if ($workoutPlan?->status !== 'generated') {
            return null;
        }

        return [
            'name' => $workoutPlan->workout_name,
            'type' => $workoutPlan->workout_type,
            'duration_minutes' => $workoutPlan->estimated_duration_minutes,
            'thumbnail_url' => $workoutPlan->thumbnailUrl(),
        ];
    }
}
