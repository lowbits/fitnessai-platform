<?php

namespace App\Http\Resources\Api\V3;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Recipe */
class RecipeFavoriteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->user()?->locale;

        return [
            'id' => $this->id,
            'name' => $this->localizedName($locale),
            'slug' => $this->localizedSlug($locale),
            'calories' => (int) $this->calories,
            'protein_g' => (int) $this->protein_g,
            'image_url' => $this->image_url,
            'thumbnail_url' => $this->thumbnail_url,
        ];
    }
}
