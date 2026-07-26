<?php

namespace App\Http\Resources\Api\V3;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Rich recipe shape used when surfacing existing recipes as swap suggestions.
 * Carries everything mobile needs to render a card AND link the replacement
 * directly to this recipe (skipping AI regeneration).
 *
 * @mixin Recipe
 */
class RecipeSuggestionResource extends JsonResource
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
            'image_url' => $this->image_url,
            'thumbnail_url' => $this->thumbnail_url,
            'calories' => (int) $this->calories,
            'protein_g' => (int) $this->protein_g,
            'carbs_g' => (int) $this->carbs_g,
            'fat_g' => (int) $this->fat_g,
            'prep_time_minutes' => $this->prep_time_minutes,
            'cook_time_minutes' => $this->cook_time_minutes,
        ];
    }
}
