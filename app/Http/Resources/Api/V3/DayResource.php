<?php

namespace App\Http\Resources\Api\V3;

use App\Enums\DayAccess;
use App\ValueObjects\DayPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DayPlan
 */
class DayResource extends JsonResource
{
    // No "data" envelope — the client reads the day fields at the top level.
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $mealPlan = $this->mealPlan;

        return [
            'plan_id' => $this->plan->id,
            'total_days' => $this->totalDays,
            'date' => $this->date->toDateString(),
            'day_name' => $this->date->format('l'),

            'access' => $this->access->value,
            'status' => $this->status->value,

            'meals' => DayMealResource::collection($mealPlan?->meals ?? collect()),
            'daily_totals' => $mealPlan ? [
                'calories' => $mealPlan->total_calories,
                'protein_g' => $mealPlan->total_protein_g,
                'carbs_g' => $mealPlan->total_carbs_g,
                'fat_g' => $mealPlan->total_fat_g,
            ] : null,
            'workout' => $this->workoutPlan ? new DayWorkoutResource($this->workoutPlan) : null,

            'tracked_calories' => new TrackedCaloriesResource($this->calorieTrackings),
            'tracked_workouts' => new TrackedWorkoutsResource($this->workoutTrackings),
            'completion' => new DayCompletionResource($this->completion),
            'week_strip' => $this->weekStrip,

            'plan_end_date' => $this->access === DayAccess::Expired
                ? $this->plan->end_date->format('Y-m-d')
                : null,
        ];
    }
}
