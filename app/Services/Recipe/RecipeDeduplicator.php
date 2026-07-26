<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Meilisearch\Client;
use Throwable;

/**
 * Three-layer match used at recipe upsert time to avoid creating near-duplicate
 * rows for what is essentially the same dish:
 *
 *   1. Hard structural filter in Meilisearch on (locale, primary_protein,
 *      format, meal_type). Different protein or format → never compared.
 *   2. Hybrid ranking within that bucket as a soft tiebreaker.
 *   3. Deterministic Jaccard overlap on normalized ingredients as the safety
 *      net before accepting any hit.
 */
class RecipeDeduplicator
{
    public function __construct(private readonly Client $client) {}

    /**
     * Returns the matching recipe to reuse, or null if no candidate clears the
     * structural + ingredient-overlap thresholds. Without primaryProtein OR
     * format we cannot dedup safely.
     *
     * @param  array<int, array{name?: string}>  $ingredients
     */
    public function findSimilar(
        string $name,
        array $ingredients,
        string $locale,
        ?string $primaryProtein,
        ?string $format,
        string $mealType,
        float $similarityThreshold = 0.85,
        float $ingredientOverlap = 0.6,
    ): ?Recipe {
        if (! $primaryProtein || ! $format) {
            return null;
        }

        $candidateNames = $this->normalizeIngredients($ingredients);
        if (empty($candidateNames)) {
            return null;
        }

        $hits = $this->hybridSearch($name, $candidateNames, $locale, $primaryProtein, $format, $mealType, $similarityThreshold);

        foreach ($hits as $hit) {
            $recipe = Recipe::find($hit['id']);
            if (! $recipe) {
                continue;
            }

            $recipeNames = $this->normalizeIngredients($recipe->ingredients ?? []);
            if ($this->overlap($candidateNames, $recipeNames) >= $ingredientOverlap) {
                return $recipe;
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $candidateNames
     * @return list<array{id: int}>
     */
    private function hybridSearch(
        string $name,
        array $candidateNames,
        string $locale,
        string $primaryProtein,
        string $format,
        string $mealType,
        float $similarityThreshold,
    ): array {
        $query = trim($name.' '.implode(' ', $candidateNames));
        $filter = implode(' AND ', [
            'source_locale = '.json_encode($locale),
            'primary_protein = '.json_encode($primaryProtein),
            'format = '.json_encode($format),
            'meal_types = '.json_encode($mealType),
        ]);

        try {
            return $this->client->index('recipes')->search($query, [
                'filter' => $filter,
                'hybrid' => ['embedder' => 'default', 'semanticRatio' => 0.7],
                'limit' => 5,
                'showRankingScore' => true,
                'rankingScoreThreshold' => $similarityThreshold,
            ])->getHits();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @param  array<int, array{name?: string}>  $ingredients
     * @return list<string>
     */
    private function normalizeIngredients(array $ingredients): array
    {
        return collect($ingredients)
            ->pluck('name')
            ->filter()
            ->map(fn ($n) => mb_strtolower(trim($n)))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $a
     * @param  list<string>  $b
     */
    private function overlap(array $a, array $b): float
    {
        if (empty($a) || empty($b)) {
            return 0.0;
        }

        $intersect = count(array_intersect($a, $b));
        $union = count(array_unique(array_merge($a, $b)));

        return $union === 0 ? 0.0 : $intersect / $union;
    }
}
