<?php

namespace App\Services\Recipe;

use App\Models\Meal;
use App\Models\Recipe;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Meilisearch\Client;
use Throwable;

class RecipeFinder
{
    public function __construct(
        private readonly Client $client,
        private readonly FoodTermTranslator $translator,
    ) {}

    /**
     * Find a single stored Recipe matching the slot's constraints.
     *
     * @param  list<string>  $allowedProteins  PrimaryProtein enum values
     * @param  list<string>  $dislikes  Lowercased ingredient names the user dislikes
     * @param  Collection<int, Meal>  $forbiddenAxes  Recipes matching any (protein, format) tuple are rejected
     * @param  list<int>  $excludeIds  Recipe IDs to skip
     * @param  array<int, int>  $affinityScores  Optional recipe_id => score map for ranking
     * @param  int|null  $targetProtein  When set, rank by combined closeness to targetKcal + this protein instead of highest protein
     */
    public function findCandidate(
        string $mealType,
        int $targetKcal,
        string $locale,
        array $allowedProteins,
        array $dislikes,
        Collection $forbiddenAxes,
        array $excludeIds = [],
        array $affinityScores = [],
        ?int $targetProtein = null,
    ): ?Recipe {
        return $this->findCandidates(
            $mealType, $targetKcal, $locale, $allowedProteins, $dislikes, $forbiddenAxes,
            excludeIds: $excludeIds,
            affinityScores: $affinityScores,
            limit: 1,
            targetProtein: $targetProtein,
        )->first();
    }

    /**
     * Top-N variant: returns up to $limit recipes matching the slot's constraints.
     *
     * The caller decides whether to apply a cooldown (via $excludeIds) and how to
     * rank candidates (via $affinityScores). RecipeFinder is search-only — it does
     * not know about user history or favorites.
     *
     * @param  list<string>  $allowedProteins
     * @param  list<string>  $dislikes
     * @param  Collection<int, Meal>  $forbiddenAxes
     * @param  list<int>  $excludeIds  Recipe IDs to skip (current meal, cooldown, manual block)
     * @param  array<int, int>  $affinityScores  Optional recipe_id => score map for ranking
     * @return Collection<int, Recipe>
     */
    public function findCandidates(
        string $mealType,
        int $targetKcal,
        string $locale,
        array $allowedProteins,
        array $dislikes,
        Collection $forbiddenAxes,
        array $excludeIds = [],
        array $affinityScores = [],
        int $limit = 5,
        ?string $query = null,
        bool $constrainToMeal = true,
        ?int $targetProtein = null,
    ): Collection {
        $filter = $this->buildFilter($mealType, $targetKcal, $locale, $allowedProteins, $dislikes, $forbiddenAxes, $constrainToMeal);
        $hits = $this->search($filter, $query);
        $hitIds = $hits->pluck('id')->diff($excludeIds);

        if ($hitIds->isEmpty()) {
            return collect();
        }

        $candidates = Recipe::query()->whereIn('id', $hitIds)->get();

        if (filled($query)) {
            $order = $hits->pluck('id')->values()->flip();

            return $candidates
                ->sortBy(fn (Recipe $r) => $order[$r->id] ?? PHP_INT_MAX)
                ->take($limit)
                ->values();
        }

        $ranked = $candidates->shuffle();

        $ranked = $targetProtein !== null
            ? $ranked->sortBy(fn (Recipe $r) => abs((int) $r->calories - $targetKcal) / max(1, $targetKcal)
                + abs((int) $r->protein_g - $targetProtein) / max(1, $targetProtein))
            : $ranked->sortByDesc(fn (Recipe $r) => (int) $r->protein_g);

        return $ranked
            ->sortByDesc(fn (Recipe $r) => $affinityScores[$r->id] ?? 0)
            ->take($limit)
            ->values();
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
        Collection $forbiddenAxes,
        bool $constrainToMeal = true,
    ): string {
        $filters = ['source_locale = '.json_encode($locale)];

        // A named-dish wish drops the slot/calorie fit so the requested dish can
        // surface; diet and dislikes stay enforced either way.
        if ($constrainToMeal) {
            $filters[] = 'meal_types = '.json_encode($mealType);
            $filters[] = sprintf('calories %d TO %d', (int) round($targetKcal * 0.85), (int) round($targetKcal * 1.15));
        }

        if (! empty($allowedProteins)) {
            $filters[] = 'primary_protein IN ['.collect($allowedProteins)->map(fn ($p) => json_encode($p))->implode(',').']';
        }

        foreach ($this->translator->toEnglishMany($dislikes) as $dislike) {
            $filters[] = 'ingredient_names != '.json_encode($dislike);
        }

        $forbiddenAxes
            ->map(fn (Meal $m) => [$m->primary_protein, $m->format])
            ->filter(fn (array $pair) => $pair[0] !== null && $pair[1] !== null)
            ->unique(fn (array $pair) => $pair[0].'|'.$pair[1])
            ->each(function (array $pair) use (&$filters) {
                $filters[] = 'NOT (primary_protein = '.json_encode($pair[0]).' AND format = '.json_encode($pair[1]).')';
            });

        return implode(' AND ', $filters);
    }

    /**
     * @return Collection<int, array<string, mixed>> Meilisearch hits with id + raw payload
     */
    private function search(string $filter, ?string $query = null): Collection
    {
        try {
            $params = [
                'filter' => $filter,
                'limit' => 50,
            ];

            if (filled($query)) {
                $params['hybrid'] = ['embedder' => 'default', 'semanticRatio' => 0.7];
            }

            $results = $this->client->index('recipes')->search($query ?? '', $params);

            return collect($results->getHits());
        } catch (Throwable $e) {
            Log::warning('[RecipeFinder] Meilisearch search failed', [
                'filter' => $filter,
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }
}
