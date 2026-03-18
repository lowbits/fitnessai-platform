<?php

namespace App\Http\Controllers\Api\V2\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateWorkoutExerciseRequest;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;

class UpdateWorkoutExerciseController extends Controller
{
    public function __invoke(UpdateWorkoutExerciseRequest $request, WorkoutPlan $workout, int $exerciseId): JsonResponse
    {
        $workoutExercise = $workout->exercises()->where('id', $exerciseId)->first();

        if (! $workoutExercise) {
            return response()->json(['message' => 'Exercise not found'], 404);
        }

        $workoutExercise->update($request->only(['sets', 'reps', 'duration_seconds', 'tempo', 'rpe', 'rest_seconds']));

        return response()->json([
            'message' => 'Exercise updated successfully',
            'exercise' => [
                'id' => $workoutExercise->id,
                'exercise_id' => $workoutExercise->exercise_id,
                'order' => $workoutExercise->order,
                'sets' => $workoutExercise->sets,
                'reps' => $workoutExercise->reps,
                'duration_seconds' => $workoutExercise->duration_seconds,
                'tempo' => $workoutExercise->tempo,
                'rpe' => $workoutExercise->rpe,
                'rest_seconds' => $workoutExercise->rest_seconds,
            ],
        ]);
    }
}
