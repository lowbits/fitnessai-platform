<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V3\RecipeFavoriteResource;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class RecipeFavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $favorites = $request->user()->favoriteRecipes()->get();

        return RecipeFavoriteResource::collection($favorites);
    }

    public function store(Request $request, Recipe $recipe): JsonResponse
    {
        $request->user()->favoriteRecipes()->syncWithoutDetaching([$recipe->id]);

        Log::info('[RecipeFavorite] Favorited', [
            'user_id' => $request->user()->id,
            'recipe_id' => $recipe->id,
        ]);

        return response()->json(['message' => 'Recipe favorited.'], 201);
    }

    public function destroy(Request $request, Recipe $recipe): JsonResponse
    {
        $request->user()->favoriteRecipes()->detach($recipe->id);

        Log::info('[RecipeFavorite] Unfavorited', [
            'user_id' => $request->user()->id,
            'recipe_id' => $recipe->id,
        ]);

        return response()->json(['message' => 'Recipe unfavorited.']);
    }
}
