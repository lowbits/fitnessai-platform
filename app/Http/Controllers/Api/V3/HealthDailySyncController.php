<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\Health\CreditActiveEnergy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\HealthDailySyncRequest;
use App\Http\Resources\Api\V3\HealthSyncResource;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
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

        // The fytrr workout only lands in the measured active energy when we write
        // it back, so only then is it double-counted (it's already in the goal) and
        // only then do we subtract it. Without write-back there is nothing to remove.
        $trainingKcal = $user->workout_writeback_enabled
            ? $this->completedTrainingKcal($user, $validated['date'])
            : 0;

        $creditableEnergy = max(0, $validated['active_energy_kcal'] - $trainingKcal);

        $values = [
            'active_energy_kcal' => $validated['active_energy_kcal'],
            'steps' => $validated['steps'],
            'workouts' => $validated['workouts'],
            'credited_kcal' => $credit($creditableEnergy),
            'synced_at' => now(),
        ];

        try {
            $metric = $user->healthDailyMetrics()->updateOrCreate(
                ['date' => $validated['date']],
                $values,
            );
        } catch (UniqueConstraintViolationException) {
            // A concurrent first sync for the same day won the insert race — update the winner.
            $metric = $user->healthDailyMetrics()->where('date', $validated['date'])->firstOrFail();
            $metric->update($values);
        }

        $user->markHealthConnected();

        return HealthSyncResource::make($metric)->response()->setStatusCode(200);
    }

    /**
     * Total estimated energy of the user's completed fytrr workouts on a date —
     * the training already priced into the daily goal, subtracted before crediting.
     */
    private function completedTrainingKcal(User $user, string $date): int
    {
        return (int) $user->workoutTrackings()
            ->whereNotNull('completed_at')
            ->whereDate('completed_at', $date)
            ->with('workoutPlan:id,estimated_calories_burned')
            ->get()
            ->sum(fn ($tracking) => (int) ($tracking->workoutPlan?->estimated_calories_burned ?? 0));
    }
}
