<?php

namespace App\Http\Resources\Api\V3;

use App\Models\HealthDailyMetric;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HealthDailyMetric
 */
class HealthSyncResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $enabled = (bool) $request->user()->activity_credit_enabled;

        return [
            'date' => $this->date->toDateString(),
            'active_energy_kcal' => $this->active_energy_kcal,
            'steps' => $this->steps,
            'workouts' => $this->workouts ?? [],
            'credited_kcal' => $enabled ? $this->credited_kcal : 0,
            'enabled' => $enabled,
            'synced_at' => $this->synced_at?->toISOString(),
        ];
    }
}
