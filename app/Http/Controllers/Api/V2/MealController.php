<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MealController extends Controller
{
    use Concerns\MapsThumbnails;

    public function show(Request $request, Meal $meal): JsonResponse
    {
        Gate::authorize('view', $meal);

        $user = $request->user();

        return response()->json([
            'id' => $meal->id,
            'recipe_id' => $meal->recipe_id,
            'name' => $meal->name,
            'type' => ucfirst($meal->type),
            'image_url' => $meal->image_url,
            'thumbnail_url' => $this->mealThumbnail($meal),
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
            'completed_at' => $meal->completed_at,
            'is_favorited' => $user->hasFavorited($meal->recipe_id),
        ]);
    }

    /**
     * Delete a meal
     */
    public function destroy(Meal $meal): JsonResponse
    {

        // Authorize using policy
        Gate::authorize('delete', $meal);

        // Soft delete the meal
        $meal->delete();

        return response()->json([
            'message' => 'Meal deleted successfully',
            'deleted_at' => $meal->deleted_at->toISOString(),
        ], 200);
    }
}
