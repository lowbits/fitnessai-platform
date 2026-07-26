<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property Collection $resource
 */
class TrackedWorkoutsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $entries = $this->resource->map(fn ($tracking) => [
            'id' => $tracking->id,
            'workout_plan_id' => $tracking->workout_plan_id,
            'started_at' => $tracking->started_at->toISOString(),
            'completed_at' => $tracking->completed_at?->toISOString(),
            'is_completed' => $tracking->completed_at !== null,
            'feeling_rate' => $tracking->feeling_rate,
            'notes' => $tracking->notes,
            'exercises' => $tracking->exercises->map(fn ($exercise) => [
                'id' => $exercise->id,
                'workout_plan_exercise_id' => $exercise->workout_plan_exercise_id,
                'order' => $exercise->order,
                'notes' => $exercise->notes,
                'sets' => $exercise->sets->map(fn ($set) => [
                    'id' => $set->id,
                    'set_number' => $set->set_number,
                    'reps' => $set->reps,
                    'weight' => $set->weight,
                    'duration' => $set->duration,
                    'rpe' => $set->rpe,
                    'notes' => $set->notes,
                ])->values()->all(),
            ])->values()->all(),
        ])->values()->all();

        return [
            'entries' => $entries,
            'count' => $this->resource->count(),
        ];
    }
}
