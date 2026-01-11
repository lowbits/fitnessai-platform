<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoveWorkoutController extends Controller
{
    /**
     * Move workout to tomorrow and replace current day with rest day
     */
    public function __invoke(Request $request, WorkoutPlan $workout): JsonResponse
    {
        $user = $request->user();

        // Verify the workout belongs to user's plan
        if ($workout->plan->user_id !== $user->id) {
            return $this->unauthorizedResponse();
        }

        // Check if workout is a rest day
        if ($workout->workout_type === 'rest') {
            return $this->cannotMoveRestDayResponse();
        }

        $force = $request->boolean('force', false);

        try {
            return DB::transaction(function () use ($user, $workout, $force) {
                $workout->load(['plan', 'exercises']);

                $tomorrowDate = $workout->date->copy()->addDay();
                $tomorrowDayNumber = $workout->day_number + 1;

                // Validate tomorrow is within plan duration
                $validationResponse = $this->validatePlanDuration($workout->plan, $tomorrowDayNumber);
                if ($validationResponse) {
                    return $validationResponse;
                }

                // Check for existing tomorrow workout
                $tomorrowWorkout = $this->findTomorrowWorkout($workout->plan_id, $tomorrowDayNumber);

                if ($tomorrowWorkout && !$force) {
                    return $this->conflictResponse($tomorrowWorkout);
                }

                // Handle force replacement if needed
                $replacedWorkout = $this->handleForceReplacement($tomorrowWorkout, $user);

                // Create new workout for tomorrow
                $newWorkout = $this->replicateWorkout($workout, $tomorrowDate, $tomorrowDayNumber);

                // Replicate exercises
                $exercisesCount = $this->replicateExercises($workout->exercises, $newWorkout->id);

                // Convert current workout to rest day
                $this->convertToRestDay($workout);

                $this->logWorkoutMove($user, $workout, $newWorkout, $replacedWorkout);

                return $this->successResponse($workout, $newWorkout, $exercisesCount, $replacedWorkout);
            });
        } catch (\Exception $e) {
            return $this->handleError($e, $user, $workout);
        }
    }

    /**
     * Validate if tomorrow is within plan duration
     */
    private function validatePlanDuration($plan, int $tomorrowDayNumber): ?JsonResponse
    {
        if ($tomorrowDayNumber > $plan->duration_days) {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'Cannot move workout beyond plan duration',
                'plan_end_date' => $plan->end_date->format('Y-m-d'),
            ], 400);
        }

        return null;
    }

    /**
     * Find tomorrow's workout if it exists
     */
    private function findTomorrowWorkout(int $planId, int $dayNumber): ?WorkoutPlan
    {
        return WorkoutPlan::where('plan_id', $planId)
            ->where('day_number', $dayNumber)
            ->first();
    }

    /**
     * Handle force replacement of existing tomorrow workout
     */
    private function handleForceReplacement(?WorkoutPlan $tomorrowWorkout, $user): ?array
    {
        if (!$tomorrowWorkout) {
            return null;
        }

        $replacedWorkout = [
            'id' => $tomorrowWorkout->id,
            'name' => $tomorrowWorkout->workout_name,
            'type' => $tomorrowWorkout->workout_type,
            'date' => $tomorrowWorkout->date->format('Y-m-d'),
        ];

        $tomorrowWorkout->exercises()->delete();
        $tomorrowWorkout->delete();

        Log::info('Existing tomorrow workout replaced', [
            'user_id' => $user->id,
            'replaced_workout_id' => $tomorrowWorkout->id,
            'replaced_workout_name' => $tomorrowWorkout->workout_name,
        ]);

        return $replacedWorkout;
    }

    /**
     * Replicate workout for tomorrow
     */
    private function replicateWorkout(WorkoutPlan $workout, $tomorrowDate, int $tomorrowDayNumber): WorkoutPlan
    {
        $newWorkout = $workout->replicate();
        $newWorkout->date = $tomorrowDate;
        $newWorkout->day_number = $tomorrowDayNumber;
        $newWorkout->save();

        return $newWorkout;
    }

    /**
     * Replicate exercises to new workout
     */
    private function replicateExercises($exercises, int $newWorkoutId): int
    {
        $count = 0;

        foreach ($exercises as $exercise) {
            $newExercise = $exercise->replicate();
            $newExercise->workout_plan_id = $newWorkoutId;
            $newExercise->save();
            $count++;
        }

        return $count;
    }

    /**
     * Convert workout to rest day
     */
    private function convertToRestDay(WorkoutPlan $workout): void
    {
        $workout->exercises()->delete();
        $workout->update([
            'workout_name' => 'Rest Day',
            'workout_type' => 'rest',
            'estimated_duration_minutes' => 0,
            'estimated_calories_burned' => 0,
            'difficulty' => 'easy',
            'description' => 'Take a rest day to recover. Stay hydrated and focus on mobility.',
            'muscle_groups' => [],
            'status' => 'generated',
        ]);
    }

    /**
     * Log workout move operation
     */
    private function logWorkoutMove($user, WorkoutPlan $originalWorkout, WorkoutPlan $newWorkout, ?array $replacedWorkout): void
    {
        Log::info('Workout moved to tomorrow', [
            'user_id' => $user->id,
            'original_workout_id' => $originalWorkout->id,
            'new_workout_id' => $newWorkout->id,
            'original_date' => $originalWorkout->date->format('Y-m-d'),
            'new_date' => $newWorkout->date->format('Y-m-d'),
            'replaced_workout' => $replacedWorkout !== null,
        ]);
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'You do not have access to this workout',
        ], 403);
    }

    /**
     * Return cannot move rest day response
     */
    private function cannotMoveRestDayResponse(): JsonResponse
    {
        return response()->json([
            'error' => 'Invalid operation',
            'message' => 'Cannot move a rest day',
        ], 400);
    }

    /**
     * Return conflict response
     */
    private function conflictResponse(WorkoutPlan $tomorrowWorkout): JsonResponse
    {
        return response()->json([
            'error' => 'Conflict',
            'message' => 'Tomorrow already has a workout scheduled. Use force=true to replace it.',
            'tomorrow_workout' => [
                'id' => $tomorrowWorkout->id,
                'name' => $tomorrowWorkout->workout_name,
                'type' => $tomorrowWorkout->workout_type,
            ],
        ], 409);
    }

    /**
     * Return success response
     */
    private function successResponse(
        WorkoutPlan $restDay,
        WorkoutPlan $movedWorkout,
        int $exercisesCount,
        ?array $replacedWorkout
    ): JsonResponse {
        $response = [
            'message' => 'Workout moved to tomorrow successfully',
            'rest_day' => [
                'id' => $restDay->id,
                'date' => $restDay->date->format('Y-m-d'),
                'name' => $restDay->workout_name,
                'type' => $restDay->workout_type,
                'description' => $restDay->description,
            ],
            'moved_workout' => [
                'id' => $movedWorkout->id,
                'date' => $movedWorkout->date->format('Y-m-d'),
                'name' => $movedWorkout->workout_name,
                'type' => $movedWorkout->workout_type,
                'duration_minutes' => $movedWorkout->estimated_duration_minutes,
                'exercises_count' => $exercisesCount,
            ],
        ];

        if ($replacedWorkout !== null) {
            $response['replaced_workout'] = $replacedWorkout;
        }

        return response()->json($response, 200);
    }

    /**
     * Handle error and return error response
     */
    private function handleError(\Exception $e, $user, WorkoutPlan $workout): JsonResponse
    {
        Log::error('Failed to move workout to tomorrow', [
            'user_id' => $user->id,
            'workout_id' => $workout->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Server error',
            'message' => 'Failed to move workout. Please try again.',
        ], 500);
    }
}

