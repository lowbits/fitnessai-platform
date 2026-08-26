<?php

namespace App\Http\Controllers\Api\V2;

use App\Actions\Health\EstimateWorkoutEnergy;
use App\Http\Controllers\Controller;
use App\Models\WorkoutTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $weightKg = $request->user()->getCurrentWeight();

        return response()->json([
            'data' => $trackings->map(fn ($tracking) => $this->formatTracking($tracking, $weightKg)),
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
            'exercises.*.exercise_id' => ['required', 'exists:workout_plan_exercises,id'],
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

            if (! empty($validated['exercises'])) {
                foreach ($validated['exercises'] as $exerciseData) {
                    $trackingExercise = $tracking->exercises()->create([
                        'workout_plan_exercise_id' => $exerciseData['exercise_id'],
                        'order' => $exerciseData['order'] ?? 0,
                        'notes' => $exerciseData['notes'] ?? null,
                    ]);

                    if (! empty($exerciseData['sets'])) {
                        foreach ($exerciseData['sets'] as $setData) {
                            $trackingExercise->sets()->create($setData);
                        }
                    }
                }
            }

            return $tracking->load(['workoutPlan', 'exercises.exercise', 'exercises.sets']);
        });

        return response()->json([
            'data' => $this->formatTracking($tracking, $request->user()->getCurrentWeight()),
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
            'data' => $this->formatTracking($workoutTracking, $request->user()->getCurrentWeight()),
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
            'exercises.*.exercise_id' => ['required', 'exists:workout_plan_exercises,id'],
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
                        'workout_plan_exercise_id' => $exerciseData['exercise_id'],
                        'order' => $exerciseData['order'] ?? 0,
                        'notes' => $exerciseData['notes'] ?? null,
                    ]);

                    if (! empty($exerciseData['sets'])) {
                        foreach ($exerciseData['sets'] as $setData) {
                            $trackingExercise->sets()->create($setData);
                        }
                    }
                }
            }
        });

        $workoutTracking->load(['workoutPlan', 'exercises.exercise', 'exercises.sets']);

        return response()->json([
            'data' => $this->formatTracking($workoutTracking, $request->user()->getCurrentWeight()),
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
     * The completed session length in minutes, or null if it isn't finished.
     */
    private function sessionMinutes(WorkoutTracking $tracking): ?int
    {
        if (! $tracking->started_at || ! $tracking->completed_at) {
            return null;
        }

        $minutes = (int) round($tracking->started_at->diffInSeconds($tracking->completed_at) / 60);

        return $minutes > 0 ? $minutes : null;
    }

    /**
     * The energy to hand back for HealthKit write-back: the stored plan estimate,
     * or a MET-based fallback from the actual session length and the user's weight.
     */
    private function workoutEnergyKcal(WorkoutTracking $tracking, ?float $weightKg): ?int
    {
        $plan = $tracking->workoutPlan;

        if (! $plan) {
            return null;
        }

        if ($plan->estimated_calories_burned !== null) {
            return (int) $plan->estimated_calories_burned;
        }

        $minutes = $this->sessionMinutes($tracking) ?? (int) $plan->estimated_duration_minutes;

        return app(EstimateWorkoutEnergy::class)($plan->workout_type, $minutes, $weightKg);
    }

    /**
     * Format tracking data for response.
     */
    private function formatTracking(WorkoutTracking $tracking, ?float $weightKg = null): array
    {
        return [
            'id' => $tracking->id,
            'workout_plan_id' => $tracking->workout_plan_id,
            'started_at' => $tracking->started_at?->toISOString(),
            'completed_at' => $tracking->completed_at?->toISOString(),
            'notes' => $tracking->notes,
            'feeling_rate' => $tracking->feeling_rate,
            'exercises' => $tracking->exercises->map(fn ($exercise) => [
                'id' => $exercise->id,
                'workout_plan_exercise_id' => $exercise->workout_plan_exercise_id,
                'exercise_name' => $exercise->exercise?->name,
                'order' => $exercise->order,
                'notes' => $exercise->notes,
                'sets' => $exercise->sets->map(fn ($set) => [
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
                'estimated_calories_burned' => $this->workoutEnergyKcal($tracking, $weightKg),
            ] : null,
            'created_at' => $tracking->created_at?->toISOString(),
            'updated_at' => $tracking->updated_at?->toISOString(),
        ];
    }
}
