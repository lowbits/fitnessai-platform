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

        // Get exercises with their original names
        $exercises = $workout->exercises()->orderBy('order')->get();
        $originalNames = $exercises->pluck('original_name')->unique()->filter()->all();

        // Get latest tracking exercises for all original names in one query
        // This is more performant than querying per exercise
        $latestTrackings = collect();

        if (!empty($originalNames)) {
            $latestTrackings = WorkoutTrackingExercise::query()
                ->select('workout_tracking_exercises.*')
                ->join('workout_trackings', 'workout_tracking_exercises.workout_tracking_id', '=', 'workout_trackings.id')
                ->join('exercises', 'workout_tracking_exercises.exercise_id', '=', 'exercises.id')
                ->where('workout_trackings.user_id', $user->id)
                ->whereIn('exercises.original_name', $originalNames)
                ->whereNotNull('workout_trackings.completed_at')
                ->with(['sets' => fn($query) => $query->orderBy('set_number'), 'exercise:id,original_name', 'workoutTracking:id,completed_at'])
                ->get()
                ->groupBy(fn($item) => $item->exercise->original_name)
                ->map(fn($group) => $group->sortByDesc(fn($item) => $item->workoutTracking->completed_at)->first());
        }

        // Format exercises
        $formattedExercises = $exercises->map(function ($exercise) use ($latestTrackings) {
                $latestTracking = null;

                if ($exercise->original_name && isset($latestTrackings[$exercise->original_name])) {
                    $trackingExercise = $latestTrackings[$exercise->original_name];

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
            'exercises' => $formattedExercises,
            'exercises_count' => count($formattedExercises),
        ]);
    }

}
