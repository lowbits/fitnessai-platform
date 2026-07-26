<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Meal
 */
class DayMealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status ?? 'generated',
            'name' => $this->name,
            'type' => ucfirst($this->type),
            'image' => $this->image ?? "{$this->type}_placeholder",
            'thumbnail_url' => $this->thumbnail_url,
            'calories' => $this->calories,
            'protein_g' => $this->protein_g,
            'carbs_g' => $this->carbs_g,
            'fat_g' => $this->fat_g,
            'is_completed' => $this->completed_at !== null,
            'completed_at' => $this->completed_at?->toISOString(),
        ];
    }
}
