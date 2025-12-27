<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealController extends Controller
{
    /**
     * Get detailed meal information
     */
    public function show(Request $request, int $mealId): JsonResponse
    {
        $user = $request->user();

        // Get meal from database
        $meal = Meal::find($mealId);

        if (!$meal) {
            return response()->json([
                'error' => 'Meal not found',
                'message' => 'The requested meal does not exist',
            ], 404);
        }

        // Verify the meal belongs to user's plan
        $mealPlan = $meal->mealPlan;
        if (!$mealPlan || $mealPlan->plan->user_id !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have access to this meal',
            ], 403);
        }

        return response()->json([
            'id' => $meal->id,
            'name' => $meal->name,
            'type' => ucfirst($meal->type),
            'image' => $meal->image ?? $meal->type,
            'description' => $meal->description,

            'nutrition' => [
                'calories' => $meal->calories,
                'protein_g' => $meal->protein_g,
                'carbs_g' => $meal->carbs_g,
                'fat_g' => $meal->fat_g,
                'fiber_g' => $meal->fiber_g,
                'sugar_g' => $meal->sugar_g,
            ],

            'ingredients' => $meal->ingredients ?? [],
            'instructions' => $meal->instructions ?? [],

            'prep_time_minutes' => $meal->prep_time_minutes,
            'cook_time_minutes' => $meal->cook_time_minutes,
            'total_time_minutes' => ($meal->prep_time_minutes ?? 0) + ($meal->cook_time_minutes ?? 0),
            'difficulty' => $meal->difficulty ?? 'Medium',
            'servings' => $meal->servings ?? 1,

            'tags' => $meal->tags ?? [],
            'allergens' => $meal->allergens ?? [],
        ]);
    }
}

