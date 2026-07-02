<?php

namespace App\Services\Recipe;

use App\Enums\Unit;
use App\Jobs\GenerateRecipeImage;
use App\Models\Recipe;
use App\Support\RecipeIngredientHash;

class RecipeUpserter
{
    public function __construct(private readonly RecipeDeduplicator $deduplicator) {}

    /**
     * Find an existing Recipe by hash or semantic dedup, or create a new one.
     * Dispatches the image-generation job for newly-created recipes outside the
     * testing environment.
     *
     * @param  array<string, mixed>  $data  Same shape SaveMealPlanTool/SeedCommand produce
     */
    public function upsert(array $data, string $locale, string $mealType): Recipe
    {
        $data['ingredients'] = $this->normalizeUnits($data['ingredients'] ?? []);
        $hash = RecipeIngredientHash::compute($data['ingredients'], $locale);

        if ($existing = Recipe::query()->where('ingredient_hash', $hash)->where('source_locale', $locale)->first()) {
            return $existing;
        }

        $similar = $this->deduplicator->findSimilar(
            $data['name'],
            $data['ingredients'],
            $locale,
            $data['primary_protein'] ?? null,
            $data['format'] ?? null,
            $mealType,
        );

        if ($similar) {
            return $similar;
        }

        $recipe = Recipe::query()->create([
            'ingredient_hash' => $hash,
            'source_locale' => $locale,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'ingredients' => $data['ingredients'],
            'instructions' => $data['instructions'] ?? [],
            'prep_time_minutes' => $data['prep_time_minutes'] ?? null,
            'cook_time_minutes' => $data['cook_time_minutes'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'Medium',
            'servings' => 1,
            'calories' => $data['calories'],
            'protein_g' => $data['protein_g'],
            'carbs_g' => $data['carbs_g'],
            'fat_g' => $data['fat_g'],
            'fiber_g' => $data['fiber_g'] ?? null,
            'sugar_g' => $data['sugar_g'] ?? null,
            'tags' => $data['tags'] ?? [],
            'allergens' => $data['allergens'] ?? [],
            'meal_types' => [$mealType],
            'primary_protein' => $data['primary_protein'] ?? null,
            'cuisine' => $data['cuisine'] ?? null,
            'format' => $data['format'] ?? null,
            'hero_veg' => $data['hero_veg'] ?? null,
            'is_verified' => false,
            'needs_translation' => false,
        ]);

        if (! app()->environment('testing')) {
            GenerateRecipeImage::dispatch($recipe);
        }

        return $recipe;
    }

    /**
     * @param  array<int, array{name?: string, amount?: string, unit?: string}>  $ingredients
     * @return array<int, array{name?: string, amount?: string, unit: string}>
     */
    private function normalizeUnits(array $ingredients): array
    {
        return array_map(function (array $ing): array {
            $ing['unit'] = Unit::normalize($ing['unit'] ?? null);

            return $ing;
        }, $ingredients);
    }
}
