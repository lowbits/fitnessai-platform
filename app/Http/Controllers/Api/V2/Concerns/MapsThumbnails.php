<?php

namespace App\Http\Controllers\Api\V2\Concerns;

trait MapsThumbnails
{
    protected function mealThumbnail($meal): string
    {
        return $meal->thumbnail_url;
    }
}
