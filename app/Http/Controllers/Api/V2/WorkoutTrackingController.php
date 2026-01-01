<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\WorkoutTracking;
use App\Models\WorkoutTrackingExercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkoutTrackingController extends Controller
{
    /**
     * Display a listing of the user's workout trackings.
     */
    public function index(Request $request): JsonResponse
    {
        $trackings = $request->user()
            ->workoutTrackings()
            ->with(['workoutPlan', 'exercises.exercise', 'exercises.sets'])
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'data' => $trackings->map(fn($tracking) => $this->formatTracking($tracking)),
        ]);
    }

    /**
     * Store a newly created workout tracking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workout_plan_id' => ['required', 'exists:workout_plans,id'],
            'started_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date', 'after:started_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'feeling_rate' => ['nullable', 'integer', 'min:1', 'max:5'],
            'exercises' => ['nullable', 'array'],
            'exercises.*.exercise_id' => ['required', 'exists:exercises,id'],
            'exercises.*.order' => ['nullable', 'integer', 'min:0'],
            'exercises.*.notes' => ['nullable', 'string', 'max:500'],
            'exercises.*.sets' => ['nullable', 'array'],
            'exercises.*.sets.*.set_number' => ['required', 'integer', 'min:1'],
            'exercises.*.sets.*.reps' => ['nullable', 'integer', 'min:0'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0'],
            'exercises.*.sets.*.duration' => ['nullable', 'integer', 'min:0'],
            'exercises.*.sets.*.rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'exercises.*.sets.*.notes' => ['nullable', 'string', 'max:200'],
        ]);

        $tracking = DB::transaction(function () use ($request, $validated) {
            $tracking = $request->user()->workoutTrackings()->create([
                'workout_plan_id' => $validated['workout_plan_id'],
                'started_at' => $validated['started_at'],
                'completed_at' => $validated['completed_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'feeling_rate' => $validated['feeling_rate'] ?? null,
            ]);

            if (!empty($validated['exercises'])) {
                foreach ($validated['exercises'] as $exerciseData) {
                    $trackingExercise = $tracking->exercises()->create([
                        'exercise_id' => $exerciseData['exercise_id'],
                        'order' => $exerciseData['order'] ?? 0,
                        'notes' => $exerciseData['notes'] ?? null,
                    ]);

                    if (!empty($exerciseData['sets'])) {
                        foreach ($exerciseData['sets'] as $setData) {
                            $trackingExercise->sets()->create($setData);
                        }
                    }
                }
            }

            return $tracking->load(['workoutPlan', 'exercises.exercise', 'exercises.sets']);
        });

        return response()->json([
            'data' => $this->formatTracking($tracking),
        ], 201);
    }

    /**
     * Display the specified workout tracking.
     */
    public function show(Request $request, WorkoutTracking $workoutTracking): JsonResponse
    {
        // Authorization check
        if ($workoutTracking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $workoutTracking->load(['workoutPlan', 'exercises.exercise', 'exercises.sets']);

        return response()->json([
            'data' => $this->formatTracking($workoutTracking),
        ]);
    }

    /**
     * Update the specified workout tracking.
     */
    public function update(Request $request, WorkoutTracking $workoutTracking): JsonResponse
    {
        // Authorization check
        if ($workoutTracking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'completed_at' => ['nullable', 'date', 'after:started_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'feeling_rate' => ['nullable', 'integer', 'min:1', 'max:5'],
            'exercises' => ['nullable', 'array'],
            'exercises.*.exercise_id' => ['required', 'exists:exercises,id'],
            'exercises.*.order' => ['nullable', 'integer', 'min:0'],
            'exercises.*.notes' => ['nullable', 'string', 'max:500'],
            'exercises.*.sets' => ['nullable', 'array'],
            'exercises.*.sets.*.set_number' => ['required', 'integer', 'min:1'],
            'exercises.*.sets.*.reps' => ['nullable', 'integer', 'min:0'],
            'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0'],
            'exercises.*.sets.*.duration' => ['nullable', 'integer', 'min:0'],
            'exercises.*.sets.*.rpe' => ['nullable', 'integer', 'min:1', 'max:10'],
            'exercises.*.sets.*.notes' => ['nullable', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($workoutTracking, $validated) {
            $workoutTracking->update([
                'completed_at' => $validated['completed_at'] ?? $workoutTracking->completed_at,
                'notes' => $validated['notes'] ?? $workoutTracking->notes,
                'feeling_rate' => $validated['feeling_rate'] ?? $workoutTracking->feeling_rate,
            ]);

            if (isset($validated['exercises'])) {
                // Delete existing exercises and create new ones
                $workoutTracking->exercises()->delete();

                foreach ($validated['exercises'] as $exerciseData) {
                    $trackingExercise = $workoutTracking->exercises()->create([
                        'exercise_id' => $exerciseData['exercise_id'],
                        'order' => $exerciseData['order'] ?? 0,
                        'notes' => $exerciseData['notes'] ?? null,
                    ]);

                    if (!empty($exerciseData['sets'])) {
                        foreach ($exerciseData['sets'] as $setData) {
                            $trackingExercise->sets()->create($setData);
                        }
                    }
                }
            }
        });

        $workoutTracking->load(['workoutPlan', 'exercises.exercise', 'exercises.sets']);

        return response()->json([
            'data' => $this->formatTracking($workoutTracking),
        ]);
    }

    /**
     * Remove the specified workout tracking.
     */
    public function destroy(Request $request, WorkoutTracking $workoutTracking): JsonResponse
    {
        // Authorization check
        if ($workoutTracking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $workoutTracking->delete();

        return response()->json(null, 204);
    }

    /**
     * Format tracking data for response.
     */
    private function formatTracking(WorkoutTracking $tracking): array
    {
        return [
            'id' => $tracking->id,
            'workout_plan_id' => $tracking->workout_plan_id,
            'started_at' => $tracking->started_at?->toISOString(),
            'completed_at' => $tracking->completed_at?->toISOString(),
            'notes' => $tracking->notes,
            'feeling_rate' => $tracking->feeling_rate,
            'exercises' => $tracking->exercises->map(fn($exercise) => [
                'id' => $exercise->id,
                'exercise_id' => $exercise->exercise_id,
                'exercise_name' => $exercise->exercise?->name,
                'order' => $exercise->order,
                'notes' => $exercise->notes,
                'sets' => $exercise->sets->map(fn($set) => [
                    'id' => $set->id,
                    'set_number' => $set->set_number,
                    'reps' => $set->reps,
                    'weight' => $set->weight,
                    'duration' => $set->duration,
                    'rpe' => $set->rpe,
                    'notes' => $set->notes,
                ])->toArray(),
            ])->toArray(),
            'workout_plan' => $tracking->workoutPlan ? [
                'id' => $tracking->workoutPlan->id,
                'workout_name' => $tracking->workoutPlan->workout_name,
                'workout_type' => $tracking->workoutPlan->workout_type,
                'date' => $tracking->workoutPlan->date?->format('Y-m-d'),
            ] : null,
            'created_at' => $tracking->created_at?->toISOString(),
            'updated_at' => $tracking->updated_at?->toISOString(),
        ];
    }
}

