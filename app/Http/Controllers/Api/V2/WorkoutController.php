<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTrackingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    /**
     * Get detailed workout information
     */
    public function show(Request $request, int $workoutId): JsonResponse
    {
        $user = $request->user();

        // Get workout from database with exercises
        $workout = WorkoutPlan::with(['exercises'])->findOrFail($workoutId);

        // Verify the workout belongs to user's plan
        if ($workout->plan->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have access to this workout',
            ], 403);
        }

        // Get the latest workout tracking for this workout and user
        $latestWorkoutTracking = $workout->trackings()
            ->where('user_id', $user->id)
            ->latest('completed_at')
            ->first();

        // Get tracking exercise IDs if we have a latest tracking
        $trackingExerciseIds = $latestWorkoutTracking
            ? $latestWorkoutTracking->exercises()->pluck('exercise_id', 'id')->toArray()
            : [];

        // Format exercises
        $exercises = $workout->exercises()
            ->orderBy('order')
            ->get()
            ->map(function ($exercise) use ($trackingExerciseIds) {
                // Find the tracking exercise for this exercise
                $trackingExerciseId = array_search($exercise->id, $trackingExerciseIds);
                $latestTracking = null;

                if ($trackingExerciseId !== false) {
                    $trackingExercise = WorkoutTrackingExercise::with(['sets' => function ($query) {
                        $query->orderBy('set_number');
                    }])->find($trackingExerciseId);

                    if ($trackingExercise) {
                        $latestTracking = [
                            'notes' => $trackingExercise->notes,
                            'sets' => $trackingExercise->sets->map(fn($set) => [
                                'set_number' => $set->set_number,
                                'reps' => $set->reps,
                                'duration' => $set->duration,
                                'weight' => $set->weight,
                            ])->all(),
                        ];
                    }
                }

                return [
                    'id' => $exercise->id,
                    'order' => $exercise->order,
                    'name' => $exercise->name,
                    'original_name' => $exercise->original_name,
                    'type' => $exercise->type,
                    'description' => $exercise->description,
                    'instructions' => $exercise->instructions,
                    'sets' => $exercise->sets,
                    'reps' => $exercise->reps,
                    'duration_seconds' => $exercise->duration_seconds,
                    'rest_seconds' => $exercise->rest_seconds,
                    'tempo' => $exercise->tempo,
                    'weight_recommendation' => $exercise->weight_recommendation,
                    'muscle_groups' => $exercise->muscle_groups ?? [],
                    'equipment' => $exercise->equipment ?? [],
                    'form_cues' => $exercise->form_cues,
                    'alternatives' => $exercise->alternatives ?? [],
                    'difficulty' => $exercise->difficulty,
                    'video_url' => $exercise->video_url,
                    'image' => $exercise->image,
                    'latest_tracking' => $latestTracking,
                ];
            })->values()->all();

        return response()->json([
            'id' => $workout->id,
            'name' => $workout->workout_name,
            'type' => $workout->workout_type,
            'description' => $workout->description,
            'estimated_duration_minutes' => $workout->estimated_duration_minutes,
            'estimated_calories_burned' => $workout->estimated_calories_burned,
            'difficulty' => $workout->difficulty,
            'muscle_groups' => $workout->muscle_groups ?? [],
            'exercises' => $exercises,
            'exercises_count' => count($exercises),
        ]);
    }
}

