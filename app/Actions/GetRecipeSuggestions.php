<?php

namespace App\Actions;

use App\Ai\Agents\RecipeTaglineAgent;
use App\Enums\DietaryPreference;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Meilisearch\Client;

class GetRecipeSuggestions
{
    public function __construct(private readonly Client $client) {}

    public function execute(
        ?string $dietaryPreference = null,
        array $dislikes = [],
        ?string $cookingTime = null,
        ?string $mealType = null,
        int $limit = 8,
        string $locale = 'en',
    ): Collection {
        $cacheKey = 'recipe_suggestions:v1:'.md5(json_encode([
            'dietary_preference' => $dietaryPreference,
            'dislikes' => $dislikes,
            'cooking_time' => $cookingTime,
            'meal_type' => $mealType,
            'limit' => $limit,
            'locale' => $locale,
        ]));

        // Log dislikes to learn what users enter — review periodically to improve synonyms
        if (! empty($dislikes)) {
            Log::channel('single')->info('[RecipeSuggestions] Dislikes requested', [
                'dislikes' => $dislikes,
                'dietary_preference' => $dietaryPreference,
                'locale' => $locale,
            ]);
        }

        return Cache::remember($cacheKey, now()->addHours(24), function () use (
            $dietaryPreference, $dislikes, $cookingTime, $mealType, $limit, $locale
        ) {
            $filters = $this->buildFilters($dietaryPreference, $dislikes, $cookingTime, $mealType);

            $results = $this->client->index('recipes')->search('', [
                'filter' => implode(' AND ', $filters),
                'limit' => $limit * 2,
            ]);

            return collect($results->getHits())
                ->shuffle()
                ->take($limit)
                ->map(fn (array $hit) => $this->formatHit($hit, $locale))
                ->values();
        });
    }

    private function buildFilters(
        ?string $dietaryPreference,
        array $dislikes,
        ?string $cookingTime,
        ?string $mealType,
    ): array {
        $filters = [];

        if ($mealType) {
            $filters[] = "meal_types = '".$this->sanitize($mealType)."'";
        }

        if ($cookingTime) {
            $maxMinutes = match ($cookingTime) {
                'quick' => 15,
                'normal' => 30,
                default => null,
            };

            if ($maxMinutes) {
                $filters[] = "total_time_minutes <= {$maxMinutes}";
            }
        }

        // Use DietaryPreference enum for excluded foods
        $dietary = DietaryPreference::tryFrom($dietaryPreference ?? '');
        if ($dietary) {
            foreach ($dietary->excludedFoods() as $food) {
                $filters[] = "allergens != '".$this->sanitize($food)."'";
            }
        }

        // Exclude user-specific dislikes — check both allergens and ingredients
        foreach ($dislikes as $dislike) {
            $dislike = trim($dislike);
            if (! $dislike) {
                continue;
            }

            foreach ($this->synonyms(strtolower($dislike)) as $term) {
                $sanitized = $this->sanitize($term);
                $filters[] = "allergens != '{$sanitized}'";
                $filters[] = "ingredient_names != '{$sanitized}'";
            }
        }

        return $filters;
    }

    /**
     * Expand a dislike to its synonyms (same thing, different words).
     *
     * @return list<string>
     */
    private function synonyms(string $dislike): array
    {
        $map = [
            // Dairy
            'lactose' => ['lactose', 'dairy', 'milk'],
            'dairy' => ['dairy', 'lactose', 'milk'],
            'milk' => ['milk', 'dairy', 'lactose'],

            // Eggs
            'egg' => ['egg', 'eggs'],
            'eggs' => ['eggs', 'egg'],

            // Nuts
            'peanut' => ['peanut', 'peanuts'],
            'peanuts' => ['peanuts', 'peanut'],

            // Soy
            'soy' => ['soy', 'soya', 'tofu'],
            'soya' => ['soya', 'soy', 'tofu'],
            'tofu' => ['tofu', 'soy', 'soya'],

            // Seafood
            'shrimp' => ['shrimp', 'prawns'],
            'prawns' => ['prawns', 'shrimp'],
            'fish' => ['fish', 'salmon', 'cod', 'tuna'],
            'salmon' => ['salmon', 'fish'],
            'cod' => ['cod', 'fish'],
            'tuna' => ['tuna', 'fish'],

            // Meat
            'pork' => ['pork', 'schwein'],
            'schwein' => ['schwein', 'pork'],
            'beef' => ['beef', 'rind'],
            'rind' => ['rind', 'beef'],
            'chicken' => ['chicken', 'hähnchen', 'huhn'],

            // Gluten
            'wheat' => ['wheat', 'gluten'],
            'gluten' => ['gluten', 'wheat'],
        ];

        return $map[$dislike] ?? [$dislike];
    }

    private function sanitize(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9_ -]/', '', $value);
    }

    private function formatHit(array $hit, string $locale): array
    {
        $baseUrl = config('services.r2.public_url');
        $name = $hit['translations'][$locale] ?? $hit['name'];

        return [
            'id' => $hit['id'],
            'name' => $name,
            'name_en' => $hit['name'],
            'tagline' => $this->getTagline($hit['id'], $hit['name'], $locale),
            'image' => $hit['image_full'] ? "{$baseUrl}/{$hit['image_full']}" : null,
            'image_full' => $hit['image_full'] ?? null,
            'image_isolated' => $hit['image_isolated'] ?? null,
            'calories' => $hit['calories'],
            'protein_g' => (int) ($hit['protein_g'] ?? 0),
            'cooking_time_minutes' => $hit['total_time_minutes'] ?? 0,
            'meal_types' => $hit['meal_types'] ?? [],
        ];
    }

    private function getTagline(int $id, string $name, string $locale): string
    {
        return Cache::rememberForever(
            "recipe_tagline:{$id}:{$locale}",
            fn () => trim((string) (new RecipeTaglineAgent($locale))->prompt($name))
        );
    }
}
