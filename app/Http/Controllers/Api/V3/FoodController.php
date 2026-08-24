<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\ResolveFoodNutrition;
use App\Enums\FoodSource;
use App\Enums\MealType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\StoreFoodRequest;
use App\Http\Resources\Api\V3\FoodResource;
use App\Http\Resources\Api\V3\RecentFoodResource;
use App\Models\CalorieTracking;
use App\Models\Food;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

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

    /**
     * Create a custom food for the authenticated user.
     */
    public function store(StoreFoodRequest $request): JsonResponse
    {
        $food = Food::create([
            ...$request->validated(),
            'source' => FoodSource::Custom,
            'user_id' => $request->user()->id,
            'verified' => true,
        ]);

        return FoodResource::make($food)->response()->setStatusCode(201);
    }

    /**
     * List the user's recently logged foods, optionally for one meal.
     */
    public function recent(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'meal' => ['nullable', Rule::enum(MealType::class)],
        ]);

        $latestIds = CalorieTracking::query()
            ->where('user_id', $request->user()->id)
            ->when($validated['meal'] ?? null, fn ($query, $meal) => $query->where('meal_type', $meal))
            ->groupByRaw('COALESCE(external_id, meal_name)')
            ->selectRaw('MAX(id) as id')
            ->orderByDesc('id')
            ->limit(20)
            ->pluck('id');

        $entries = CalorieTracking::whereIn('id', $latestIds)->orderByDesc('id')->get();

        return RecentFoodResource::collection($entries);
    }
}
