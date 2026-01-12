<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\RescheduleWorkoutRequest;
use App\Models\WorkoutPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RescheduleWorkoutController extends Controller
{
    /**
     * Reschedule workout to a specific date and replace current day with rest day
     */
    public function __invoke(RescheduleWorkoutRequest $request, WorkoutPlan $workout): JsonResponse
    {
        // Authorization is already handled by RescheduleWorkoutRequest::authorize()
        $user = $request->user();
        $targetDateString = $request->getTargetDate();
        $force = $request->shouldForce();

        try {
            return DB::transaction(function () use ($user, $workout, $targetDateString, $force) {
                $workout->load(['plan', 'exercises']);


                // Parse target date (always present due to validation)
                $targetDate = Carbon::parse($targetDateString)->startOfDay();

                // Calculate target day number
                $targetDayNumber = $workout->plan->start_date->diffInDays($targetDate) + 1;

                // Validate target date
                $validationResponse = $this->validateTargetDate($workout, $targetDate, $targetDayNumber);
                if ($validationResponse) {
                    return $validationResponse;
                }


                // Check for existing workout on target date
                $targetWorkout = $this->findWorkoutByDayNumber($workout->plan_id, $targetDayNumber);

                if ($targetWorkout && $targetWorkout->workout_type !== 'rest' && !$force) {
                    return $this->conflictResponse($targetWorkout, $targetDate);
                }

                // Handle force replacement if needed (or automatic rest day replacement)
                $replacedWorkout = $this->handleForceReplacement($targetWorkout, $user);

                // Create new workout for target date
                $newWorkout = $this->replicateWorkout($workout, $targetDate, $targetDayNumber);

                // Replicate exercises
                $exercisesCount = $this->replicateExercises($workout->exercises, $newWorkout->id);

                // Convert current workout to rest day
                $this->convertToRestDay($workout);

                $this->logWorkoutReschedule($user, $workout, $newWorkout, $replacedWorkout);

                return $this->successResponse($workout, $newWorkout, $exercisesCount, $replacedWorkout);
            });
        } catch (\Exception $e) {
            return $this->handleError($e, $user, $workout);
        }
    }

    /**
     * Validate target date for rescheduling
     */
    private function validateTargetDate(WorkoutPlan $workout, Carbon $targetDate, int $targetDayNumber): ?JsonResponse
    {
        $plan = $workout->plan;

        // Cannot reschedule to the same day
        if ($targetDate->isSameDay($workout->date)) {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'Cannot reschedule to the same date',
                'current_date' => $workout->date->format('Y-m-d'),
            ], 400);
        }

        // Note: Past date validation (target_date >= today) is already handled
        // by RescheduleWorkoutRequest validation rule 'after_or_equal:today'

        // Must be within plan duration
        if ($targetDayNumber < 1 || $targetDayNumber > $plan->duration_days) {
            return response()->json([
                'error' => 'Invalid operation',
                'message' => 'Target date is outside plan duration',
                'target_date' => $targetDate->format('Y-m-d'),
                'plan_start_date' => $plan->start_date->format('Y-m-d'),
                'plan_end_date' => $plan->end_date->format('Y-m-d'),
            ], 400);
        }

        return null;
    }

    /**
     * Find workout by day number
     */
    private function findWorkoutByDayNumber(int $planId, int $dayNumber): ?WorkoutPlan
    {
        return WorkoutPlan::where('plan_id', $planId)
            ->where('day_number', $dayNumber)
            ->first();
    }

    /**
     * Handle force replacement of existing target date workout
     */
    private function handleForceReplacement(?WorkoutPlan $targetWorkout, $user): ?array
    {
        if (!$targetWorkout) {
            return null;
        }

        $replacedWorkout = [
            'id' => $targetWorkout->id,
            'name' => $targetWorkout->workout_name,
            'type' => $targetWorkout->workout_type,
            'date' => $targetWorkout->date->format('Y-m-d'),
        ];

        $targetWorkout->exercises()->delete();
        $targetWorkout->delete();

        Log::info('Existing target date workout replaced', [
            'user_id' => $user->id,
            'replaced_workout_id' => $targetWorkout->id,
            'replaced_workout_name' => $targetWorkout->workout_name,
        ]);

        return $replacedWorkout;
    }

    /**
     * Replicate workout for target date
     */
    private function replicateWorkout(WorkoutPlan $workout, Carbon $targetDate, int $targetDayNumber): WorkoutPlan
    {
        $newWorkout = $workout->replicate();
        $newWorkout->date = $targetDate;
        $newWorkout->day_number = $targetDayNumber;
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
     * Log workout reschedule operation
     */
    private function logWorkoutReschedule($user, WorkoutPlan $originalWorkout, WorkoutPlan $newWorkout, ?array $replacedWorkout): void
    {
        Log::info('Workout rescheduled', [
            'user_id' => $user->id,
            'original_workout_id' => $originalWorkout->id,
            'new_workout_id' => $newWorkout->id,
            'original_date' => $originalWorkout->date->format('Y-m-d'),
            'new_date' => $newWorkout->date->format('Y-m-d'),
            'replaced_workout' => $replacedWorkout !== null,
        ]);
    }


    /**
     * Return conflict response
     */
    private function conflictResponse(WorkoutPlan $targetWorkout, Carbon $targetDate): JsonResponse
    {
        return response()->json([
            'error' => 'Conflict',
            'message' => 'Target date already has a workout scheduled. Use force=true to replace it.',
            'target_date' => $targetDate->format('Y-m-d'),
            'existing_workout' => [
                'id' => $targetWorkout->id,
                'name' => $targetWorkout->workout_name,
                'type' => $targetWorkout->workout_type,
            ],
        ], 409);
    }

    /**
     * Return success response
     */
    private function successResponse(
        WorkoutPlan $restDay,
        WorkoutPlan $rescheduledWorkout,
        int $exercisesCount,
        ?array $replacedWorkout
    ): JsonResponse {
        $response = [
            'message' => 'Workout rescheduled successfully',
            'rest_day' => [
                'id' => $restDay->id,
                'date' => $restDay->date->format('Y-m-d'),
                'name' => $restDay->workout_name,
                'type' => $restDay->workout_type,
                'description' => $restDay->description,
            ],
            'rescheduled_workout' => [
                'id' => $rescheduledWorkout->id,
                'date' => $rescheduledWorkout->date->format('Y-m-d'),
                'name' => $rescheduledWorkout->workout_name,
                'type' => $rescheduledWorkout->workout_type,
                'duration_minutes' => $rescheduledWorkout->estimated_duration_minutes,
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
        Log::error('Failed to reschedule workout', [
            'user_id' => $user->id,
            'workout_id' => $workout->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Server error',
            'message' => 'Failed to reschedule workout. Please try again.',
        ], 500);
    }
}

