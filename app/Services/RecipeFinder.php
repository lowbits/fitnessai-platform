<?php

namespace App\Services;

use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Support\Collection;
use Meilisearch\Client;
use Throwable;

class RecipeFinder
{
    public function __construct(private readonly Client $client) {}

    /**
     * @param  list<string>  $allowedProteins  PrimaryProtein enum values
     * @param  list<string>  $dislikes  Lowercased ingredient names the user dislikes
     * @param  Collection<int, Meal>  $forbiddenAxes  Prior meals this week — recipes matching their (protein, format) tuple are rejected
     */
    public function findCandidate(
        string $mealType,
        int $targetKcal,
        string $locale,
        array $allowedProteins,
        array $dislikes,
        Collection $forbiddenAxes
    ): ?Recipe {
        $filter = $this->buildFilter($mealType, $targetKcal, $locale, $allowedProteins, $dislikes, $forbiddenAxes);

        $hitIds = $this->search($filter);

        return $hitIds->isEmpty() ? null : Recipe::query()->whereIn('id', $hitIds)->inRandomOrder()->first();
    }

    /**
     * Look up an existing Recipe by semantic similarity, used at upsert time to
     * dedup near-duplicates the ingredient_hash misses (e.g. "Hähnchen-Reis-Bowl"
     * vs "Hähnchen-Reis-Schüssel" — same dish, different word).
     */
    public function findSimilar(string $name, array $ingredients, string $locale, float $similarityThreshold = 0.85): ?Recipe
    {
        $ingredientNames = collect($ingredients)->pluck('name')->filter()->implode(' ');
        $query = trim($name.' '.$ingredientNames);

        if ($query === '') {
            return null;
        }

        try {
            $results = $this->client->index('recipes')->search($query, [
                'filter' => 'source_locale = '.json_encode($locale),
                'hybrid' => ['embedder' => 'default', 'semanticRatio' => 0.9],
                'limit' => 1,
                'showRankingScore' => true,
                'rankingScoreThreshold' => $similarityThreshold,
            ]);

            $hits = $results->getHits();
        } catch (Throwable) {
            return null;
        }

        return empty($hits) ? null : Recipe::find($hits[0]['id']);
    }

    /**
     * @param  list<string>  $allowedProteins
     * @param  list<string>  $dislikes
     * @param  Collection<int, Meal>  $forbiddenAxes
     */
    private function buildFilter(
        string $mealType,
        int $targetKcal,
        string $locale,
        array $allowedProteins,
        array $dislikes,
        Collection $forbiddenAxes
    ): string {
        $filters = [
            'meal_types = '.json_encode($mealType),
            'source_locale = '.json_encode($locale),
            sprintf('calories %d TO %d', (int) round($targetKcal * 0.85), (int) round($targetKcal * 1.15)),
        ];

        if (! empty($allowedProteins)) {
            $filters[] = 'primary_protein IN ['.collect($allowedProteins)->map(fn ($p) => json_encode($p))->implode(',').']';
        }

        foreach ($dislikes as $dislike) {
            $filters[] = 'ingredient_names != '.json_encode(mb_strtolower(trim($dislike)));
        }

        $forbiddenTuples = $forbiddenAxes
            ->map(fn (Meal $m) => '('.json_encode($m->primary_protein).', '.json_encode($m->format).')')
            ->filter()
            ->unique();

        foreach ($forbiddenTuples as $tuple) {
            [$protein, $format] = explode(', ', $tuple, 2);
            $filters[] = 'NOT (primary_protein = '.$protein.' AND format = '.$format.')';
        }

        return implode(' AND ', $filters);
    }

    /**
     * @return Collection<int, int> Recipe IDs from Meilisearch
     */
    private function search(string $filter): Collection
    {
        try {
            $results = $this->client->index('recipes')->search('', [
                'filter' => $filter,
                'limit' => 50,
            ]);

            return collect($results->getHits())->pluck('id');
        } catch (Throwable) {
            return collect();
        }
    }
}
