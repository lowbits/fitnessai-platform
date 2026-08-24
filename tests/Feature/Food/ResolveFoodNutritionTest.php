<?php

use App\Actions\ResolveFoodNutrition;
use App\Enums\FoodSource;
use App\Models\Food;
use Illuminate\Support\Facades\Http;

function offResponse(array $nutriments, array $extra = []): array
{
    return [
        'status' => 1,
        'product' => array_merge([
            'code' => '4029764001807',
            'product_name' => 'Club-Mate',
            'nutriments' => $nutriments,
        ], $extra),
    ];
}

test('it enriches a barcode from the OFF API and caches it', function () {
    Http::fake(['*' => Http::response(offResponse([
        'energy-kcal_100g' => 20,
        'proteins_100g' => 0,
        'carbohydrates_100g' => 5,
    ]))]);

    $food = app(ResolveFoodNutrition::class)->resolve('4029764001807');

    expect($food->kcal)->toBe(20.0);
    expect($food->carbs_g)->toBe(5.0);
    expect($food->source)->toBe(FoodSource::OpenFoodFacts);
    expect(Food::where('barcode', '4029764001807')->count())->toBe(1);
});

test('it returns the cached food without hitting the API again', function () {
    Food::factory()->create(['barcode' => '123', 'source' => FoodSource::OpenFoodFacts, 'kcal' => 42]);
    Http::fake();

    $food = app(ResolveFoodNutrition::class)->resolve('123');

    expect($food->kcal)->toBe(42.0);
    Http::assertNothingSent();
});

test('it keeps a genuine zero-calorie product', function () {
    Http::fake(['*' => Http::response(offResponse(['energy-kcal_100g' => 0]))]);

    $food = app(ResolveFoodNutrition::class)->resolve('0000000000001');

    expect($food->kcal)->toBe(0.0);
});

test('it returns null when nutrition is unknown', function () {
    Http::fake(['*' => Http::response(offResponse([]))]);

    expect(app(ResolveFoodNutrition::class)->resolve('999'))->toBeNull();
    expect(Food::count())->toBe(0);
});

test('it returns null when the product is unknown to OFF', function () {
    Http::fake(['*' => Http::response(['status' => 0])]);

    expect(app(ResolveFoodNutrition::class)->resolve('000'))->toBeNull();
});
