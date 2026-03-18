<?php

namespace App\Http\Controllers\Api\V2\Workouts;

use App\Http\Controllers\Api\V2\Concerns\MapsExercises;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWorkoutExerciseRequest;
use App\Models\Exercise;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;

class AddWorkoutExerciseController extends Controller
{
    use MapsExercises;

    public function __invoke(AddWorkoutExerciseRequest $request, WorkoutPlan $workout): JsonResponse
    {
        $exercise = Exercise::findOrFail($request->input('exercise_id'));

        $nextOrder = ($workout->exercises()->max('order') ?? 0) + 1;

        $workoutExercise = $workout->exercises()->create([
            'exercise_id' => $exercise->id,
            'type' => $exercise->type,
            'order' => $nextOrder,
            'sets' => $request->input('sets'),
            'reps' => $request->input('reps'),
            'duration_seconds' => $request->input('duration_seconds'),
            'tempo' => $request->input('tempo'),
            'rpe' => $request->input('rpe'),
            'rest_seconds' => $request->input('rest_seconds'),
        ]);

        $workoutExercise->load('exercise');

        return response()->json([
            'message' => 'Exercise added successfully',
            'exercise' => $this->mapExerciseToResponse($workoutExercise),
        ], 201);
    }
}
