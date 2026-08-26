<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\Health\CreditActiveEnergy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\HealthDailySyncRequest;
use App\Http\Resources\Api\V3\HealthSyncResource;
use Illuminate\Http\JsonResponse;

class HealthDailySyncController extends Controller
{
    /**
     * Upsert the authenticated user's Apple Health metrics for a single day.
     *
     * Idempotent on (user_id, date): today's row updates on every sync, past
     * rows are simply re-upserted with the same data, so the day's budget
     * "freezes" structurally without any scheduled job. Always 200 — a sync is
     * an upsert, not a creation, from the client's point of view.
     */
    public function __invoke(HealthDailySyncRequest $request, CreditActiveEnergy $credit): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $metric = $user->healthDailyMetrics()->whereDate('date', $validated['date'])->first()
            ?? $user->healthDailyMetrics()->make(['date' => $validated['date']]);

        $metric->fill([
            'active_energy_kcal' => $validated['active_energy_kcal'],
            'steps' => $validated['steps'],
            'workouts' => $validated['workouts'],
            'credited_kcal' => $credit($validated['active_energy_kcal']),
            'synced_at' => now(),
        ])->save();

        $user->markHealthConnected();

        return HealthSyncResource::make($metric)->response()->setStatusCode(200);
    }
}
