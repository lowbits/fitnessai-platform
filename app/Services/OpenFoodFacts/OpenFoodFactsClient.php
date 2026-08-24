<?php

namespace App\Services\OpenFoodFacts;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenFoodFactsClient
{
    /**
     * Fetch a single product from the Open Food Facts API. Returns null when the
     * product is unknown or the upstream is temporarily unavailable; never throws.
     *
     * @return array<string, mixed>|null
     */
    public function product(string $barcode): ?array
    {
        $base = rtrim((string) config('services.openfoodfacts.api_url'), '/');

        try {
            $response = Http::withHeaders(['User-Agent' => config('services.openfoodfacts.user_agent')])
                ->timeout(8)
                ->retry(2, 200, throw: false)
                ->get("{$base}/api/v2/product/{$barcode}.json", [
                    'fields' => 'code,product_name,product_name_de,product_name_en,brands,nutriments,serving_quantity,serving_quantity_unit,images',
                ]);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return (int) $response->json('status') === 1 ? $response->json('product') : null;
    }
}
