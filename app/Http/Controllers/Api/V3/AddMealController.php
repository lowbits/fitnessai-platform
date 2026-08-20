<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\AddMealToPlan;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a chosen recipe as a new meal on today's plan — the commit half of the
 * add-meal flow (Mona proposes cards, the user picks one, this creates it).
 */
class AddMealController extends Controller
{
    public function __construct(private readonly AddMealToPlan $addMeal) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->hasFullAccessToday(), Response::HTTP_FORBIDDEN);

        $data = $request->validate([
            'recipe_id' => ['required', 'integer', 'exists:recipes,id'],
            'type' => ['required', Rule::in(['breakfast', 'lunch', 'dinner', 'snack'])],
        ]);

        $meal = $this->addMeal->execute($user, Recipe::findOrFail($data['recipe_id']), $data['type']);

        abort_if($meal === null, Response::HTTP_CONFLICT, 'No plan for today to add to.');

        return response()->json(['meal_id' => $meal->id], Response::HTTP_CREATED);
    }
}
