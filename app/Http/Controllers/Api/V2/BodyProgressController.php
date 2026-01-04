<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\BodyProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BodyProgressController extends Controller
{
    /**
     * Store a new body progress entry
     */
    public function store(Request $request): JsonResponse
    {
        $validated = Validator::validate($request->all(), [
            'weight' => 'required|numeric|min:20|max:500',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:300',
            'waist_circumference' => 'nullable|numeric|min:0|max:300',
            'chest_circumference' => 'nullable|numeric|min:0|max:300',
            'hip_circumference' => 'nullable|numeric|min:0|max:300',
            'arm_circumference' => 'nullable|numeric|min:0|max:100',
            'thigh_circumference' => 'nullable|numeric|min:0|max:150',
            'notes' => 'nullable|string|max:1000',
        ]);


        $user = $request->user();

        $bodyProgress = $user->bodyProgress()->create([
            'weight_kg' => $validated['weight'],
            'body_fat_percentage' => $validated['body_fat_percentage'] ?? null,
            'muscle_mass_kg' => $validated['muscle_mass'] ?? null,
            'waist_circumference_cm' => $validated['waist_circumference'] ?? null,
            'chest_circumference_cm' => $validated['chest_circumference'] ?? null,
            'hip_circumference_cm' => $validated['hip_circumference'] ?? null,
            'arm_circumference_cm' => $validated['arm_circumference'] ?? null,
            'thigh_circumference_cm' => $validated['thigh_circumference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_at' => now(),
        ]);

        return response()->json([
            'message' => 'Body progress tracked successfully',
            'data' => $bodyProgress,
        ], 201);
    }

    /**
     * Update an existing body progress entry
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $bodyProgress = BodyProgress::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$bodyProgress) {
            return response()->json([
                'error' => 'Body progress entry not found',
            ], 404);
        }

        // Only allow updating the fields that are provided
        $validated = Validator::validate($request->all(), [
            'weight' => 'sometimes|numeric|min:20|max:500',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:300',
            'waist_circumference' => 'nullable|numeric|min:0|max:300',
            'chest_circumference' => 'nullable|numeric|min:0|max:300',
            'hip_circumference' => 'nullable|numeric|min:0|max:300',
            'arm_circumference' => 'nullable|numeric|min:0|max:100',
            'thigh_circumference' => 'nullable|numeric|min:0|max:150',
            'notes' => 'nullable|string|max:1000',
            'recorded_at' => 'nullable|date',
        ]);


        // Map API fields to database fields
        $updateData = [];
        if ($request->has('weight')) $updateData['weight_kg'] = $validated['weight'];
        if ($request->has('body_fat_percentage')) $updateData['body_fat_percentage'] = $validated['body_fat_percentage'];
        if ($request->has('muscle_mass')) $updateData['muscle_mass_kg'] = $validated['muscle_mass'];
        if ($request->has('waist_circumference')) $updateData['waist_circumference_cm'] = $validated['waist_circumference'];
        if ($request->has('chest_circumference')) $updateData['chest_circumference_cm'] = $validated['chest_circumference'];
        if ($request->has('hip_circumference')) $updateData['hip_circumference_cm'] = $validated['hip_circumference'];
        if ($request->has('arm_circumference')) $updateData['arm_circumference_cm'] = $validated['arm_circumference'];
        if ($request->has('thigh_circumference')) $updateData['thigh_circumference_cm'] = $validated['thigh_circumference'];
        if ($request->has('notes')) $updateData['notes'] = $validated['notes'];
        if ($request->has('recorded_at')) $updateData['recorded_at'] = $validated['recorded_at'];

        $bodyProgress->update($updateData);

        return response()->json([
            'message' => 'Body progress updated successfully',
            'data' => $bodyProgress->fresh(),
        ]);
    }

    /**
     * Get user's body progress history
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = Validator::validate($request->all(), [
            'limit' => 'nullable|integer|min:1|max:100',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);



        $query = $user->bodyProgress()->orderBy('recorded_at', 'desc');

        if ($request->from) {
            $query->where('recorded_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->where('recorded_at', '<=', $request->to);
        }

        if ($request->limit) {
            $query->limit($request->limit);
        }

        $progress = $query->get();

        return response()->json([
            'data' => $progress,
            'count' => $progress->count(),
        ]);
    }

    /**
     * Get the latest body progress entry
     */
    public function latest(Request $request): JsonResponse
    {
        $user = $request->user();

        $latest = $user->bodyProgress()
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (!$latest) {
            return response()->json([
                'message' => 'No body progress entries found',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => $latest,
        ]);
    }

    /**
     * Delete a body progress entry
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $bodyProgress = BodyProgress::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$bodyProgress) {
            return response()->json([
                'error' => 'Body progress entry not found',
            ], 404);
        }

        $bodyProgress->delete();

        return response()->json([
            'message' => 'Body progress entry deleted successfully',
        ]);
    }
}

