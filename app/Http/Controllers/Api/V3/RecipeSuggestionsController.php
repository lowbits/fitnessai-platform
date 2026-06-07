<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\GetRecipeSuggestions;
use App\Enums\CookingPreference;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class RecipeSuggestionsController extends Controller
{
    public function __invoke(Request $request, GetRecipeSuggestions $action): JsonResponse
    {
        $request->validate([
            'dietary_preference' => ['sometimes', 'string'],
            'dislikes' => ['sometimes', 'string'],
            'cooking_time' => ['sometimes', new Enum(CookingPreference::class)],
            'meal_type' => ['sometimes', 'string', 'in:breakfast,lunch,dinner,snack'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'locale' => ['sometimes', 'string', 'in:en,de'],
        ]);

        $recipes = $action->execute(
            dietaryPreference: $request->input('dietary_preference'),
            dislikes: $request->input('dislikes') ? explode(',', $request->input('dislikes')) : [],
            cookingTime: $request->input('cooking_time'),
            mealType: $request->input('meal_type'),
            limit: $request->integer('limit', 8),
            locale: $request->input('locale', config('app.fallback_locale')),
        );

        return response()->json($recipes);
    }
}
