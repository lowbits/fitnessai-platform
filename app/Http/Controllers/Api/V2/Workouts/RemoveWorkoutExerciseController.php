<?php

namespace App\Http\Controllers\Api\V2\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Requests\RemoveWorkoutExerciseRequest;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Log;

class RemoveWorkoutExerciseController extends Controller
{
    public function __invoke(RemoveWorkoutExerciseRequest $request, WorkoutPlan $workout, int $exerciseId): JsonResponse
    {
        Log::debug("User {$request->user()->id} is attempting to remove exercise {$exerciseId} from workout {$workout->id}");
        $workoutExercise = WorkoutPlanExercise::where('id', $exerciseId)
            ->where('workout_plan_id', $workout->id)
            ->first();

        if (! $workoutExercise) {
            return response()->json([
                'message' => 'Exercise not found in this workout.',
            ], 404);
        }

        $removedOrder = $workoutExercise->order;

        DB::transaction(function () use ($workoutExercise, $workout, $removedOrder) {
            $workoutExercise->delete();

            $workout->exercises()
                ->where('order', '>', $removedOrder)
                ->decrement('order');
        });

        Log::debug("Exercise {$exerciseId} removed from workout {$workout->id}...");

        return response()->json([
            'message' => 'Exercise removed successfully',
        ]);
    }
}
