<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property Collection $resource
 */
class TrackedCaloriesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $entries = $this->resource->map(fn ($tracking) => [
            'id' => $tracking->id,
            'meal_id' => $tracking->meal_id,
            'external_id' => $tracking->external_id,
            'source' => $tracking->source,
            'meal_name' => $tracking->meal_name ?? $tracking->meal?->name,
            'meal_type' => $tracking->meal?->type ?? $tracking->meal_type,
            'calories' => (float) $tracking->calories,
            'protein_g' => $tracking->protein_g !== null ? (float) $tracking->protein_g : null,
            'carbs_g' => $tracking->carbs_g !== null ? (float) $tracking->carbs_g : null,
            'fat_g' => $tracking->fat_g !== null ? (float) $tracking->fat_g : null,
            'notes' => $tracking->notes,
            'tracked_at' => $tracking->created_at->toISOString(),
        ])->values()->all();

        return [
            'entries' => $entries,
            'totals' => [
                'calories' => (float) $this->resource->sum('calories'),
                'protein_g' => (float) $this->resource->sum('protein_g'),
                'carbs_g' => (float) $this->resource->sum('carbs_g'),
                'fat_g' => (float) $this->resource->sum('fat_g'),
            ],
            'count' => $this->resource->count(),
        ];
    }
}
