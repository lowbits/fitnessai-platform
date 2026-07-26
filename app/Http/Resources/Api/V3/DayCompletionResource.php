<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\ValueObjects\DayCompletion
 */
class DayCompletionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'perfect' => $this->isPerfect,
            'nutrition' => $this->nutritionMet,
            'workout' => $this->workoutDone,
        ];
    }
}
