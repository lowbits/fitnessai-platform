<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    /**
     * Get meal and workout plan for a specific date
     */
    public function getDayPlan(Request $request, string $date): JsonResponse
    {
        $user = $request->user();

        Log::info("Request for plan on date: {$date}", [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // Get user's active plan
        $plan = $user->plans()->where('status', 'active')->first();

        if (!$plan) {
            return response()->json([
                'error' => 'No active plan found',
                'message' => 'You don\'t have an active plan. Please complete onboarding first.',
            ], 404);
        }

        // Parse the requested date
        try {
            $requestDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid date format',
                'message' => 'Please provide a valid date in YYYY-MM-DD format',
            ], 400);
        }

        // Calculate day number based on plan start date
        $dayOfPlan = $plan->start_date->diffInDays($requestDate) + 1;

        // Validate day is within plan range
        if ($dayOfPlan < 1) {
            return response()->json([
                'error' => 'Invalid date',
                'message' => 'This date is before your plan start date',
                'plan_start_date' => $plan->start_date->format('Y-m-d'),
            ], 400);
        }

        $totalDays = config('plans.duration_days');
        if ($dayOfPlan > $totalDays) {
            return response()->json([
                'error' => 'Invalid date',
                'message' => 'This date is beyond your plan duration',
                'plan_end_date' => $plan->start_date->copy()->addDays($totalDays - 1)->format('Y-m-d'),
            ], 400);
        }

        // TODO: Check subscription and lock status for premium features
        // For now, allow access to all days
        $isLocked = false;

        // Get meal plan for this day
        $mealPlan = MealPlan::with('meals')
            ->where('plan_id', $plan->id)
            ->where('day_number', $dayOfPlan)
            ->first();

        // Get workout plan for this day
        $workoutPlan = WorkoutPlan::with('exercises')
            ->where('plan_id', $plan->id)
            ->where('day_number', $dayOfPlan)
            ->first();

        // Handle different meal plan statuses
        $mealsData = $this->formatMealPlanResponse($mealPlan);
        $workoutData = $this->formatWorkoutPlanResponse($workoutPlan);

        // Determine overall status
        $overallStatus = $this->determineOverallStatus($mealPlan, $workoutPlan);

        return response()->json([
            'plan_id' => $plan->id,
            'plan_day' => $dayOfPlan,
            'total_days' => $totalDays,
            'date' => $requestDate->toDateString(),
            'day_name' => $requestDate->format('l'),
            'locked' => $isLocked,
            'status' => $overallStatus,
            'meals' => $mealsData['meals'],
            'workout' => $workoutData,
            'daily_totals' => $mealsData['totals'],
            'message' => $this->getStatusMessage($overallStatus),
        ]);
    }

    /**
     * Format meal plan response
     */
    private function formatMealPlanResponse(?MealPlan $mealPlan): array
    {
        if (!$mealPlan) {
            return [
                'meals' => [],
                'totals' => null,
                'status' => 'not_generated',
            ];
        }

        if ($mealPlan->status === 'pending') {
            return [
                'meals' => [],
                'totals' => null,
                'status' => 'generating',
            ];
        }

        if ($mealPlan->status === 'failed') {
            return [
                'meals' => [],
                'totals' => null,
                'status' => 'failed',
            ];
        }

        // Format meals from database
        $meals = $mealPlan->meals->map(function ($meal) {
            return [
                'id' => $meal->id,
                'name' => $meal->name,
                'type' => ucfirst($meal->type),
                'image' => $meal->image ?? "{$meal->type}_placeholder",
                'calories' => $meal->calories,
                'protein_g' => $meal->protein_g,
                'carbs_g' => $meal->carbs_g,
                'fat_g' => $meal->fat_g,
            ];
        })->values()->all();

        return [
            'meals' => $meals,
            'totals' => [
                'calories' => $mealPlan->total_calories,
                'protein_g' => $mealPlan->total_protein_g,
                'carbs_g' => $mealPlan->total_carbs_g,
                'fat_g' => $mealPlan->total_fat_g,
            ],
            'status' => 'generated',
        ];
    }

    /**
     * Format workout plan response
     */
    private function formatWorkoutPlanResponse(?WorkoutPlan $workoutPlan): ?array
    {
        if (!$workoutPlan) {
            return null;
        }

        if ($workoutPlan->status === 'pending') {
            return [
                'status' => 'generating',
                'message' => 'Workout is being generated...',
            ];
        }

        if ($workoutPlan->status === 'failed') {
            return [
                'status' => 'failed',
                'message' => 'Failed to generate workout. Please contact support.',
            ];
        }

        return [
            'id' => $workoutPlan->id,
            'name' => $workoutPlan->workout_name,
            'type' => $workoutPlan->workout_type,
            'description' => $workoutPlan->description,
            'duration_minutes' => $workoutPlan->estimated_duration_minutes,
            'exercises' => $workoutPlan->exercises->pluck('name'),
            'exercises_count' => $workoutPlan->exercises->count(),
            'difficulty' => $workoutPlan->difficulty,
            'muscle_groups' => $workoutPlan->muscle_groups ?? [],
            'status' => 'generated',
        ];
    }

    /**
     * Determine overall status of the day
     */
    private function determineOverallStatus(?MealPlan $mealPlan, ?WorkoutPlan $workoutPlan): string
    {
        $mealStatus = $mealPlan?->status ?? 'not_generated';
        $workoutStatus = $workoutPlan?->status ?? 'not_generated';

        // If either is generating, overall is generating
        if ($mealStatus === 'pending' || $workoutStatus === 'pending') {
            return 'generating';
        }

        // If either failed, overall is partial or failed
        if ($mealStatus === 'failed' && $workoutStatus === 'failed') {
            return 'failed';
        }

        if ($mealStatus === 'failed' || $workoutStatus === 'failed') {
            return 'partial';
        }

        // If both generated, overall is generated
        if ($mealStatus === 'generated' && $workoutStatus === 'generated') {
            return 'generated';
        }

        // Otherwise, still generating
        return 'generating';
    }

    /**
     * Get user-friendly status message
     */
    private function getStatusMessage(string $status): ?string
    {
        return match($status) {
            'generating' => 'Your plan is being generated. This may take a few moments.',
            'failed' => 'Failed to generate plan. Please contact support.',
            'partial' => 'Some parts of your plan could not be generated. Please contact support.',
            'not_generated' => 'Plan for this day has not been generated yet.',
            default => null,
        };
    }
}

