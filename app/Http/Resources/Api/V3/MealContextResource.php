<?php

namespace App\Http\Resources\Api\V3;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Compact meal shape used as the "original_meal" context on the alternatives
 * endpoint — mobile shows the user what they are replacing.
 *
 * @mixin Meal
 */
class MealContextResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'thumbnail_url' => $this->thumbnail_url,
            'calories' => (int) $this->calories,
            'protein_g' => (int) $this->protein_g,
            'carbs_g' => (int) $this->carbs_g,
            'fat_g' => (int) $this->fat_g,
        ];
    }
}
