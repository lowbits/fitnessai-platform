<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkipWorkoutController extends Controller
{
    /**
     * Skip a workout by replacing it with a rest day and soft deleting it
     */
    public function __invoke(Request $request, WorkoutPlan $workoutPlan): JsonResponse
    {

        $user = $request->user();

        Gate::authorize('skip', $workoutPlan);

        // Get workout from database with exercises
        $workoutPlan = $workoutPlan->load('exercises');

        // Don't allow skipping if already a rest day
        if ($workoutPlan->workout_type === 'rest') {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'Cannot skip a rest day',
            ], 422);
        }

        // Don't allow skipping if already skipped (soft deleted)
        if ($workoutPlan->trashed()) {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'This workout has already been skipped',
            ], 422);
        }

        // Store info for response and new rest day
        $date = $workoutPlan->date;
        $dayNumber = $workoutPlan->day_number;
        $planId = $workoutPlan->plan_id;
        $originalWorkoutName = $workoutPlan->workout_name;
        $originalWorkoutType = $workoutPlan->workout_type;
        $originalId = $workoutPlan->id;
        $exercisesCount = $workoutPlan->exercises->count();

        // Check if there's already an active workout with same plan_id and day_number
        // (This shouldn't happen, but we validate to ensure data integrity)
        $existingActiveWorkout = WorkoutPlan::where('plan_id', $planId)
            ->where('day_number', $dayNumber)
            ->where('id', '!=', $workoutPlan->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingActiveWorkout) {
            return response()->json([
                'error' => 'Conflict',
                'message' => 'An active workout already exists for this day',
            ], 409);
        }

        // Soft delete the original workout
        $workoutPlan->delete();

        // Create a new rest day with the same date and day_number
        $restDay = WorkoutPlan::create([
            'plan_id' => $planId,
            'date' => $date,
            'day_number' => $dayNumber,
            'status' => 'generated',
            'workout_name' => __('workouts.active_recovery', [], $user->locale),
            'workout_type' => 'rest',
            'description' => __('workouts.rest_description', [], $user->locale),
            'estimated_duration_minutes' => 0,
        ]);

        return response()->json([
            'message' => 'Workout skipped successfully',
            'original_workout' => [
                'id' => $originalId,
                'name' => $originalWorkoutName,
                'type' => $originalWorkoutType,
                'date' => $date->format('Y-m-d'),
                'exercises_count' => $exercisesCount,
            ],
            'rest_day' => [
                'id' => $restDay->id,
                'name' => $restDay->workout_name,
                'date' => $restDay->date->format('Y-m-d'),
                'type' => $restDay->workout_type,
                'description' => $restDay->description,
            ],
            'skipped_at' => now()->toIso8601String(),
        ]);
    }
}
