<?php

namespace App\Http\Controllers\Api\V2;

use App\Actions\ReplaceMealWithRecipe;
use App\Http\Controllers\Controller;
use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ReplaceMealController extends Controller
{
    public function __invoke(Request $request, ReplaceMealWithRecipe $replaceWithRecipe, Meal $meal): JsonResponse
    {
        Gate::authorize('update', $meal);

        $validated = Validator::validate($request->all(), [
            'recipe_id' => ['sometimes', 'integer', 'exists:recipes,id'],
            'instruction' => ['required_without:recipe_id', 'string', 'max:500'],
        ]);

        if ($request->filled('recipe_id')) {
            $recipe = Recipe::query()->findOrFail($validated['recipe_id']);
            $newMeal = $replaceWithRecipe->execute($meal, $recipe);

            return response()->json([
                'message' => 'Meal replaced.',
                'meal_id' => $newMeal->id,
                'recipe_id' => $recipe->id,
            ], 200);
        }

        $meal->update(['status' => 'replacing']);
        ReplaceMealJob::dispatch($meal, $validated['instruction']);

        return response()->json([
            'message' => 'Meal replacement is being generated',
            'meal_id' => $meal->id,
            'instruction' => $validated['instruction'],
        ], 202);
    }
}
