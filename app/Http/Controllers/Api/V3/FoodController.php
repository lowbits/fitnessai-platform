<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\ResolveFoodNutrition;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V3\FoodResource;
use Illuminate\Http\JsonResponse;

class FoodController extends Controller
{
    public function __construct(private readonly ResolveFoodNutrition $resolver) {}

    /**
     * Resolve a barcode to a food with nutrition.
     */
    public function show(string $barcode): JsonResponse
    {
        $food = $this->resolver->resolve($barcode);

        if (! $food) {
            return response()->json([
                'error' => 'not_found',
                'message' => 'No nutrition found for this barcode.',
            ], 404);
        }

        return FoodResource::make($food)->response()->setStatusCode(200);
    }
}
