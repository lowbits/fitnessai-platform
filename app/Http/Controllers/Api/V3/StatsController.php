<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Services\StreakService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Progress stats for the current user (stats/progress screen).
 *
 * The streak is computed on read from tracking records (see StreakService) —
 * no stored state. This is the home to grow with all-time totals and
 * current-week progress as those land.
 */
class StatsController extends Controller
{
    public function __construct(private readonly StreakService $streaks) {}

    public function __invoke(Request $request): JsonResponse
    {
        $streak = $this->streaks->for($request->user());

        return response()->json([
            'current_streak' => $streak['current'],
            'longest_streak' => $streak['longest'],
            'last_activity_on' => $streak['last_activity_on'],
            'perfect_days' => $streak['perfect_days'],
            'score' => $streak['score'],
            'tier' => $streak['tier'],
            'next_tier' => $streak['next_tier'],
            'week' => $streak['week'],
        ]);
    }
}
