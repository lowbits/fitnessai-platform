<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ReplaceMealController extends Controller
{
    /**
     * Replace a meal with an alternative based on a recipe title instruction
     */
    public function __invoke(Request $request, Meal $meal): JsonResponse
    {
        // Authorize using policy
        Gate::authorize('update', $meal);

        // Validate input - instruction is the recipe title
        $validated = Validator::validate($request->all(), [
            'instruction' => 'required|string|max:500',
        ]);

        $meal->update(['status' => 'replacing']);

        // Dispatch the job to replace the meal with the given instruction (recipe title)
        ReplaceMealJob::dispatch($meal, $validated['instruction']);

        return response()->json([
            'message' => 'Meal replacement is being generated',
            'meal_id' => $meal->id,
            'instruction' => $validated['instruction'],
        ], 202);
    }
}

