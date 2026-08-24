<?php

namespace App\Actions;

use App\Enums\FoodSource;
use App\Models\Food;
use App\Services\OpenFoodFacts\OpenFoodFactsClient;
use App\Services\OpenFoodFacts\ProductExtractor;

class ResolveFoodNutrition
{
    public function __construct(
        private readonly OpenFoodFactsClient $client,
        private readonly ProductExtractor $extractor,
    ) {}

    /**
     * Resolve a barcode to a food, enriching from the OFF API when uncached.
     */
    public function resolve(string $barcode): ?Food
    {
        $cached = Food::where('source', FoodSource::OpenFoodFacts)->where('barcode', $barcode)->first();
        if ($cached) {
            return $cached;
        }

        $product = $this->client->product($barcode);
        if (! $product) {
            return null;
        }

        $data = $this->extractor->extract($product);
        if (blank($data) || blank($data['energy_kcal_100g'])) {
            return null;
        }

        return Food::firstOrCreate([
            'source' => FoodSource::OpenFoodFacts,
            'barcode' => $barcode,
        ], [
            'name' => $data['product_name'],
            'brand' => $data['brands'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'image_thumb_url' => $data['image_thumb_url'] ?? null,
            'kcal' => $data['energy_kcal_100g'],
            'protein_g' => $data['proteins_100g'] ?? null,
            'carbs_g' => $data['carbohydrates_100g'] ?? null,
            'fat_g' => $data['fat_100g'] ?? null,
            'fiber_g' => $data['fiber_100g'] ?? null,
            'sugar_g' => $data['sugars_100g'] ?? null,
            'sat_fat_g' => $data['saturated_fat_100g'] ?? null,
            'salt_g' => $data['salt_100g'] ?? null,
            'serving_size' => $data['serving_quantity'] ?? null,
            'serving_unit' => $data['serving_quantity_unit'] ?? null,
            'verified' => true,
        ]);
    }
}
