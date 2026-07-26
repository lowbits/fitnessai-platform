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
        $validated = $request->validate([
            'dietary_preference' => ['sometimes', 'string'],
            'dislikes' => ['sometimes', 'array'],
            'dislikes.*' => ['string', 'max:100'],
            'cooking_time' => ['sometimes', new Enum(CookingPreference::class)],
            'meal_type' => ['sometimes', 'string', 'in:breakfast,lunch,dinner,snack'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'locale' => ['sometimes', 'string', 'in:en,de'],
        ]);

        $recipes = $action->execute(
            dietaryPreference: $validated['dietary_preference'] ?? null,
            dislikes: $validated['dislikes'] ?? [],
            cookingTime: $validated['cooking_time'] ?? null,
            mealType: $validated['meal_type'] ?? null,
            limit: $validated['limit'] ?? 8,
            locale: $validated['locale'] ?? config('app.fallback_locale'),
        );

        return response()->json($recipes);
    }
}
