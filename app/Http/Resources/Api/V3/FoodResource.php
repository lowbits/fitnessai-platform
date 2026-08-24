<?php

namespace App\Http\Resources\Api\V3;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Food
 */
class FoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source->value,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'brand' => $this->brand,
            'image_url' => $this->image_url,
            'image_thumb_url' => $this->image_thumb_url,
            'kcal' => $this->kcal,
            'protein_g' => $this->protein_g,
            'carbs_g' => $this->carbs_g,
            'fat_g' => $this->fat_g,
            'fiber_g' => $this->fiber_g,
            'sugar_g' => $this->sugar_g,
            'sat_fat_g' => $this->sat_fat_g,
            'salt_g' => $this->salt_g,
            'serving_size' => $this->serving_size,
            'serving_unit' => $this->serving_unit,
        ];
    }
}
