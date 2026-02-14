<?php

namespace App\Http\Controllers\Api\V2\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddWorkoutExerciseRequest;
use App\Models\Exercise;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;

class AddWorkoutExerciseController extends Controller
{
    public function __invoke(AddWorkoutExerciseRequest $request, WorkoutPlan $workout): JsonResponse
    {
        $exercise = Exercise::findOrFail($request->input('exercise_id'));

        $nextOrder = ($workout->exercises()->max('order') ?? 0) + 1;

        $workoutExercise = $workout->exercises()->create([
            'exercise_id' => $exercise->id,
            'name' => $exercise->name,
            'type' => $exercise->type,
            'video_url' => $exercise->video_url,
            'image' => $exercise->image,
            'order' => $nextOrder,
            'sets' => $request->input('sets'),
            'reps' => $request->input('reps'),
            'duration_seconds' => $request->input('duration_seconds'),
            'tempo' => $request->input('tempo'),
            'rpe' => $request->input('rpe'),
            'rest_seconds' => $request->input('rest_seconds'),
        ]);

        return response()->json([
            'message' => 'Exercise added successfully',
            'exercise' => [
                'id' => $workoutExercise->id,
                'exercise_id' => $workoutExercise->exercise_id,
                'name' => $workoutExercise->name,
                'order' => $workoutExercise->order,
            ],
        ], 201);
    }
}
