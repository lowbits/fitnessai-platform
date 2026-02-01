<?php

namespace App\Services\OpenFoodFacts;

class ProductExtractor
{
    public function extract(array $data): ?array
    {
        $code = $data['code'] ?? null;
        $productName = $data['product_name']
            ?? $data['product_name_de']
            ?? $data['product_name_en']
            ?? null;

        if (!$code || !$productName) {
            return null;
        }

        $nutriments = $data['nutriments'] ?? [];

        return [
            'id' => $code,
            'barcode' => $code,
            'popularity_key' => $data['popularity_key'] ?? 0,
            'product_name' => $productName,
            'product_name_de' => $data['product_name_de'] ?? null,
            'product_name_en' => $data['product_name_en'] ?? null,
            'brands' => $data['brands'] ?? null,
            'image_url' => $this->buildImageUrl($code, $data),
            'image_thumb_url' => $this->buildThumbUrl($code, $data),
            'energy_kcal_100g' => $this->toFloat($nutriments['energy-kcal_100g'] ?? null),
            'proteins_100g' => $this->toFloat($nutriments['proteins_100g'] ?? null),
            'carbohydrates_100g' => $this->toFloat($nutriments['carbohydrates_100g'] ?? null),
            'fat_100g' => $this->toFloat($nutriments['fat_100g'] ?? null),
            'fiber_100g' => $this->toFloat($nutriments['fiber_100g'] ?? null),
            'sugars_100g' => $this->toFloat($nutriments['sugars_100g'] ?? null),
            'saturated_fat_100g' => $this->toFloat($nutriments['saturated-fat_100g'] ?? null),
            'salt_100g' => $this->toFloat($nutriments['salt_100g'] ?? null),
            'sodium_100g' => $this->toFloat($nutriments['sodium_100g'] ?? null),
            'nutriments' => $nutriments ?: null,
            'nutriscore_grade' => $data['nutriscore_grade'] ?? null,
            'nova_group' => $this->toInt($data['nova_group'] ?? null),
            'serving_quantity' => $this->toFloat($data['serving_quantity'] ?? $data['product_quantity'] ?? null),
            'serving_quantity_unit' => $data['serving_quantity_unit'] ?? $data['product_quantity_unit'] ?? null,
            'product_quantity' => $this->toFloat($data['product_quantity'] ?? null),
            'product_quantity_unit' => $data['product_quantity_unit'] ?? null,
            'categories' => $data['categories'] ?? null,
            'categories_tags' => $data['categories_tags'] ?? null,
            'labels_tags' => $data['labels_tags'] ?? null,
            'allergens_tags' => $data['allergens_tags'] ?? null,
            'countries_tags' => $data['countries_tags'] ?? null,
            'ingredients_text' => $data['ingredients_text_de']
                ?? $data['ingredients_text_en']
                    ?? $data['ingredients_text']
                    ?? null,
        ];
    }

    private function buildImageUrl(string $code, array $data): ?string
    {
        $imgid = $this->getFrontImageId($data);
        if (!$imgid) return null;

        $codePath = $this->codeToPath($code);
        return "https://images.openfoodfacts.org/images/products/{$codePath}/{$imgid}.400.jpg";
    }

    private function buildThumbUrl(string $code, array $data): ?string
    {
        $imgid = $this->getFrontImageId($data);
        if (!$imgid) return null;

        $codePath = $this->codeToPath($code);
        return "https://images.openfoodfacts.org/images/products/{$codePath}/{$imgid}.100.jpg";
    }

    private function getFrontImageId(array $data): ?string
    {
        $images = $data['images']['selected']['front'] ?? [];
        $frontImage = $images['de'] ?? $images['en'] ?? reset($images) ?: null;

        return $frontImage['imgid'] ?? null;
    }

    private function codeToPath(string $code): string
    {
        if (strlen($code) === 13) {
            return sprintf('%s/%s/%s/%s',
                substr($code, 0, 3),
                substr($code, 3, 3),
                substr($code, 6, 3),
                substr($code, 9)
            );
        }

        return $code;
    }

    private function toFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function toInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
