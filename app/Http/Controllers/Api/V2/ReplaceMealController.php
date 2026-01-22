<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplaceMealController extends Controller
{
    /**
     * Replace a meal with an alternative (Queue-based, original version)
     */
    public function __invoke(Request $request, int $mealId): JsonResponse
    {
        $user = $request->user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'hint' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get meal from database with relations
        $meal = Meal::with('mealPlan.plan')->find($mealId);

        if (!$meal) {
            return response()->json([
                'error' => 'Meal not found',
                'message' => 'The requested meal does not exist',
            ], 404);
        }

        // Verify the meal belongs to user's plan
        $mealPlan = $meal->mealPlan;
        if (!$mealPlan || $mealPlan->plan->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have access to this meal',
            ], 403);
        }

        // Dispatch the job to replace the meal
        $hint = $request->input('hint');
        ReplaceMealJob::dispatch($meal, $hint);

        return response()->json([
            'message' => 'Meal replacement is being generated',
            'meal_id' => $meal->id,
        ], 202);
    }
}

