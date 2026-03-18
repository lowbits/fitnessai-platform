<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\V2\Concerns\MapsExercises;
use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTrackingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    use MapsExercises;

    /**
     * Get detailed workout information
     */
    public function show(Request $request, int $workoutId): JsonResponse
    {
        $user = $request->user();

        // Get workout from database with exercises and their canonical exercise
        $workout = WorkoutPlan::with(['exercises.exercise.translations'])->findOrFail($workoutId);

        // Verify the workout belongs to user's plan
        if ($workout->plan->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have access to this workout',
            ], 403);
        }

        // Get exercises ordered
        $exercises = $workout->exercises()->with('exercise.translations')->orderBy('order')->get();
        $exerciseIds = $exercises->pluck('exercise_id')->unique()->filter()->all();

        // Get latest tracking exercises for all exercise IDs in one query
        $latestTrackings = collect();

        if (! empty($exerciseIds)) {
            $latestTrackings = WorkoutTrackingExercise::query()
                ->select('workout_tracking_exercises.*')
                ->join('workout_trackings', 'workout_tracking_exercises.workout_tracking_id', '=', 'workout_trackings.id')
                ->join('workout_plan_exercises', 'workout_tracking_exercises.workout_plan_exercise_id', '=', 'workout_plan_exercises.id')
                ->where('workout_trackings.user_id', $user->id)
                ->whereIn('workout_plan_exercises.exercise_id', $exerciseIds)
                ->whereNotNull('workout_trackings.completed_at')
                ->with(['sets' => fn ($query) => $query->orderBy('set_number'), 'exercise:id,exercise_id', 'workoutTracking:id,completed_at'])
                ->get()
                ->groupBy(fn ($item) => $item->exercise->exercise_id)
                ->map(fn ($group) => $group->sortByDesc(fn ($item) => $item->workoutTracking->completed_at)->first());
        }

        // Format exercises
        $formattedExercises = $exercises->map(function ($exercise) use ($latestTrackings) {
            $latestTracking = null;

            if ($exercise->exercise_id && isset($latestTrackings[$exercise->exercise_id])) {
                $trackingExercise = $latestTrackings[$exercise->exercise_id];

                $latestTracking = [
                    'notes' => $trackingExercise->notes,
                    'sets' => $trackingExercise->sets->map(fn ($set) => [
                        'set_number' => $set->set_number,
                        'reps' => $set->reps,
                        'duration' => $set->duration,
                        'weight' => $set->weight,
                    ])->all(),
                ];
            }

            return $this->mapExerciseToResponse($exercise, $latestTracking);
        })->values()->all();

        return response()->json([
            'id' => $workout->id,
            'name' => $workout->workout_name,
            'date' => $workout->date->format('Y-m-d'),
            'type' => $workout->workout_type,
            'description' => $workout->description,
            'estimated_duration_minutes' => $workout->estimated_duration_minutes,
            'estimated_calories_burned' => $workout->estimated_calories_burned,
            'thumbnail_url' => $workout->thumbnailUrl(),
            'difficulty' => $workout->difficulty,
            'muscle_groups' => $workout->muscle_groups ?? [],
            'equipment_details' => $workout->equipmentDetails(),
            'exercises' => $formattedExercises,
            'exercises_count' => count($formattedExercises),
        ]);
    }
}
