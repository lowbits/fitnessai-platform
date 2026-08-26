<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthSettingsController extends Controller
{
    /**
     * Toggle the Apple Health flags: whether measured activity credits the daily
     * budget, and whether completed fytrr workouts are written back to Health.
     * Either or both may be sent; the credit gate takes effect on the next
     * day-payload read without needing a re-sync.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_credit_enabled' => ['required_without_all:workout_writeback_enabled', 'boolean'],
            'workout_writeback_enabled' => ['required_without_all:activity_credit_enabled', 'boolean'],
        ]);

        $user = $request->user();
        $user->forceFill($validated)->save();

        return response()->json($user->only([
            'activity_credit_enabled',
            'workout_writeback_enabled',
        ]));
    }

    /**
     * Detach Apple Health: clear the stored metrics and connection flags. The
     * user must still revoke iOS-level access in Settings; HealthKit does not
     * allow an app to do that for them.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->disconnectHealth();

        return response()->json(null, 204);
    }
}
