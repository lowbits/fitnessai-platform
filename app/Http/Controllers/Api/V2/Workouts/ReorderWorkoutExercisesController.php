<?php

namespace App\Http\Controllers\Api\V2\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReorderWorkoutExercisesRequest;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReorderWorkoutExercisesController extends Controller
{
    public function __invoke(ReorderWorkoutExercisesRequest $request, WorkoutPlan $workout): JsonResponse
    {
        $exerciseIds = $request->input('exercise_ids');

        $workoutExerciseIds = $workout->exercises()->pluck('id')->sort()->values()->all();
        $providedIds = collect($exerciseIds)->sort()->values()->all();

        if ($workoutExerciseIds !== $providedIds) {
            return response()->json([
                'message' => "Exercise IDs must match the workout's exercises exactly.",
            ], 422);
        }

        DB::transaction(function () use ($exerciseIds, $workout) {
            foreach ($exerciseIds as $index => $exerciseId) {
                $workout->exercises()
                    ->where('id', $exerciseId)
                    ->update(['order' => $index + 1]);
            }
        });

        return response()->json([
            'message' => 'Exercises reordered successfully',
        ]);
    }
}
