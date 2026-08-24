<?php

namespace App\Http\Resources\Api\V3;

use App\Models\CalorieTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CalorieTracking
 */
class RecentFoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->meal_name,
            'external_id' => $this->external_id,
            'meal_type' => $this->meal_type?->value,
            'kcal' => (float) $this->calories,
            'protein_g' => $this->when(! is_null($this->protein_g), fn (): float => (float) $this->protein_g),
            'carbs_g' => $this->when(! is_null($this->carbs_g), fn (): float => (float) $this->carbs_g),
            'fat_g' => $this->when(! is_null($this->fat_g), fn (): float => (float) $this->fat_g),
        ];
    }
}
