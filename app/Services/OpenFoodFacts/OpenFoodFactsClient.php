<?php

namespace App\Services\OpenFoodFacts;

use Illuminate\Support\Facades\Http;

class OpenFoodFactsClient
{
    /**
     * Fetch a single product from the Open Food Facts API.
     *
     * @return array<string, mixed>|null
     */
    public function product(string $barcode): ?array
    {
        $base = rtrim((string) config('services.openfoodfacts.api_url'), '/');

        $response = Http::withHeaders(['User-Agent' => config('services.openfoodfacts.user_agent')])
            ->timeout(8)
            ->get("{$base}/api/v2/product/{$barcode}.json", [
                'fields' => 'code,product_name,product_name_de,product_name_en,brands,nutriments,serving_quantity,serving_quantity_unit,images',
            ]);

        if (! $response->ok()) {
            return null;
        }

        return (int) $response->json('status') === 1 ? $response->json('product') : null;
    }
}
