<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CalorieTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalorieTrackingController extends Controller
{
    /**
     * Display a listing of the user's calorie trackings.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $query = $request->user()
            ->calorieTrackings()
            ->with('meal')
            ->orderBy('tracked_date', 'desc')
            ->orderBy('created_at', 'desc');

        if (isset($validated['start_date'])) {
            $query->whereDate('tracked_date', '>=', $validated['start_date']);
        }



        if (isset($validated['end_date'])) {
            $query->whereDate('tracked_date', '<=', $validated['end_date']);
        }


        $trackings = $query->get();


        return response()->json([
            'data' => $trackings->map(fn($tracking) => $this->formatTracking($tracking)),
        ]);
    }

    /**
     * Store a newly created calorie tracking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meal_id' => ['nullable', 'exists:meals,id'],
            'tracked_date' => ['required', 'date'],
            'calories' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'protein_g' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'carbs_g' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'fat_g' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'meal_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $tracking = $request->user()->calorieTrackings()->create($validated);

        // If tracking a meal from the plan, mark it as completed
        if ($tracking->meal_id && $tracking->meal) {
            $tracking->meal->markAsCompleted();
        }

        $tracking->load('meal');

        return response()->json([
            'data' => $this->formatTracking($tracking),
        ], 201);
    }

    /**
     * Display the specified calorie tracking.
     */
    public function show(Request $request, CalorieTracking $calorieTracking): JsonResponse
    {
        // Authorization check
        if ($calorieTracking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $calorieTracking->load('meal');

        return response()->json([
            'data' => $this->formatTracking($calorieTracking),
        ]);
    }

    /**
     * Update the specified calorie tracking.
     */
    public function update(Request $request, CalorieTracking $calorieTracking): JsonResponse
    {
        // Authorization check
        if ($calorieTracking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'meal_id' => ['nullable', 'exists:meals,id'],
            'tracked_date' => ['sometimes', 'required', 'date'],
            'calories' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999.99'],
            'protein_g' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'carbs_g' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'fat_g' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'meal_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $calorieTracking->update($validated);
        $calorieTracking->load('meal');

        return response()->json([
            'data' => $this->formatTracking($calorieTracking),
        ]);
    }

    /**
     * Remove the specified calorie tracking.
     */
    public function destroy(Request $request, CalorieTracking $calorieTracking): JsonResponse
    {
        // Authorization check
        if ($calorieTracking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // If this was tracking a meal, reset its completed_at
        if ($calorieTracking->meal_id && $calorieTracking->meal) {
            $calorieTracking->meal->markAsIncomplete();
        }

        $calorieTracking->delete();

        return response()->json([
            'message' => 'Calorie tracking deleted successfully',
        ]);
    }

    /**
     * Format a calorie tracking for JSON response.
     */
    private function formatTracking(CalorieTracking $tracking): array
    {
        return [
            'id' => $tracking->id,
            'meal_id' => $tracking->meal_id,
            'tracked_date' => $tracking->tracked_date->format('Y-m-d'),
            'calories' => (float) $tracking->calories,
            'protein_g' => $tracking->protein_g ? (float) $tracking->protein_g : null,
            'carbs_g' => $tracking->carbs_g ? (float) $tracking->carbs_g : null,
            'fat_g' => $tracking->fat_g ? (float) $tracking->fat_g : null,
            'meal_name' => $tracking->meal_name,
            'notes' => $tracking->notes,
            'meal' => $tracking->meal ? [
                'id' => $tracking->meal->id,
                'name' => $tracking->meal->name,
                'type' => $tracking->meal->type,
            ] : null,
            'created_at' => $tracking->created_at->toISOString(),
            'updated_at' => $tracking->updated_at->toISOString(),
        ];
    }
}
