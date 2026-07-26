<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\WorkoutPlan
 */
class DayWorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->workout_name,
            'type' => $this->workout_type,
            'description' => $this->description,
            'duration_minutes' => $this->estimated_duration_minutes,
            'thumbnail_url' => $this->thumbnailUrl(),
            'exercises' => $this->exercises->map(fn ($e) => $e->exercise?->localizedName() ?? $e->name),
            'exercises_count' => $this->exercises->count(),
            'difficulty' => $this->difficulty,
            'muscle_groups' => $this->muscle_groups ?? [],
            'equipment_details' => $this->equipmentDetails(),
            'is_completed' => (bool) $this->is_completed,
            'status' => 'generated',
        ];
    }
}
