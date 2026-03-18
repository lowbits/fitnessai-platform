<?php

namespace App\Http\Controllers\Api\V2\Concerns;

trait MapsThumbnails
{
    protected function mealThumbnail($meal): string
    {
        $baseUrl = config('services.r2.public_url');

        if ($meal->image_isolated) {
            return "{$baseUrl}/{$meal->image_isolated}";
        }

        if ($meal->image_full) {
            return "{$baseUrl}/{$meal->image_full}";
        }

        $type = strtolower($meal->type);

        return "{$baseUrl}/meals/thumbnails/{$type}_placeholder.png";
    }
}
