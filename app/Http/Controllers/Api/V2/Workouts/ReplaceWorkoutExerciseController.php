<?php

namespace App\Http\Controllers\Api\V2\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReplaceWorkoutExerciseRequest;
use App\Models\Exercise;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Http\JsonResponse;

class ReplaceWorkoutExerciseController extends Controller
{
    public function __invoke(ReplaceWorkoutExerciseRequest $request, WorkoutPlan $workout, int $exerciseId): JsonResponse
    {
        $workoutExercise = WorkoutPlanExercise::where('id', $exerciseId)
            ->where('workout_plan_id', $workout->id)
            ->first();

        if (! $workoutExercise) {
            return response()->json([
                'message' => 'Exercise not found in this workout.',
            ], 404);
        }

        $newExercise = Exercise::findOrFail($request->input('exercise_id'));

        $workoutExercise->update([
            'exercise_id' => $newExercise->id,
            'name' => $newExercise->name,
            'video_url' => $newExercise->video_url,
            'image' => $newExercise->image,
        ]);

        return response()->json([
            'message' => 'Exercise replaced successfully',
        ]);
    }
}
