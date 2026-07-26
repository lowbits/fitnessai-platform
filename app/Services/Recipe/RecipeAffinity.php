<?php

namespace App\Services\Recipe;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Per-user, per-recipe affinity scoring.
 *
 * The product principle: the more a user uses the app, the better its
 * recommendations get. This service is the primitive that powers that —
 * it turns existing behavioral signals (favorites, completions, replacements)
 * into a single sortable score per recipe.
 *
 * Used by:
 *   - RecipeFinder::findCandidate — sorts candidate recipes by affinity,
 *     excludes recently-served non-favorites.
 *   - GetMealAlternativesController — orders swap suggestions by affinity.
 *
 * Weights are ASSUMPTIONS, documented as constants below. Tune when there
 * is real user data to validate against. Do not silently inline magic
 * numbers elsewhere — change them here so the assumption stays auditable.
 */
class RecipeAffinity
{
    /**
?     * Explicit "I love this" beats all other signals. Captured at onboarding
     * via the recipe-swipe screen (recipe_favorites) and any later in-app
     * favoriting action.
     */
    private const W_FAVORITE = 5;

    /**
     * One eaten meal is a mild positive. Stacks if user eats the same
     * recipe multiple times — repeated completion = reliable winner.
     */
    private const W_COMPLETED = 1;

    /**
     * User explicitly swapped this recipe out of their plan. Captured via
     * SoftDeletes (the replace flow sets deleted_at on the original meal).
     */
    private const W_REPLACED = -3;

    /**
     * User explicitly disliked the recipe at onboarding (swipe-left on the
     * recipe-swipe screen). Same magnitude as W_REPLACED — explicit negative.
     */
    private const W_DISLIKED = -3;

    /**
     * Recipes served (regardless of completion) within this window are cooled
     * down for new plans unless the user has favorited them. Two weeks is the
     * sweet spot: long enough that week 2 doesn't echo week 1, short enough
     * that we eventually surface good recipes again.
     */
    private const COOLDOWN_DAYS = 14;

    /**
     * Compute per-recipe affinity scores for a user.
     *
     * Returns a map: recipe_id => score (int). Recipes with no signals
     * are absent from the map (treat as 0 / discovery candidates).
     *
     * @return Collection<int, int>
     */
    public function scoresFor(User $user): Collection
    {
        $scores = collect();

        foreach ($user->favoriteRecipes()->pluck('recipes.id') as $id) {
            $scores[$id] = ($scores[$id] ?? 0) + self::W_FAVORITE;
        }

        foreach ($user->profile?->disliked_recipe_ids ?? [] as $id) {
            $scores[$id] = ($scores[$id] ?? 0) + self::W_DISLIKED;
        }

        $meals = Meal::query()
            ->withTrashed()
            ->whereHas('mealPlan.plan', fn ($q) => $q->where('user_id', $user->id))
            ->whereNotNull('recipe_id')
            ->get(['recipe_id', 'completed_at', 'deleted_at']);

        foreach ($meals as $meal) {
            $recipeId = $meal->recipe_id;

            if ($meal->deleted_at) {
                $scores[$recipeId] = ($scores[$recipeId] ?? 0) + self::W_REPLACED;

                continue;
            }

            if ($meal->completed_at) {
                $scores[$recipeId] = ($scores[$recipeId] ?? 0) + self::W_COMPLETED;
            }
        }

        return $scores;
    }

    /**
     * Recipe IDs that should be excluded from new-plan generation because
     * they were served recently — UNLESS the user favorited them, in which
     * case they bypass cooldown (we honor explicit positive signal).
     *
     * @return Collection<int, int>
     */
    public function cooldownIds(User $user): Collection
    {
        $cutoff = Carbon::now()->subDays(self::COOLDOWN_DAYS);

        $recentRecipeIds = Meal::query()
            ->withTrashed()
            ->whereHas('mealPlan.plan', fn ($q) => $q->where('user_id', $user->id))
            ->where('created_at', '>=', $cutoff)
            ->whereNotNull('recipe_id')
            ->pluck('recipe_id')
            ->unique();

        $favoriteIds = $user->favoriteRecipes()->pluck('recipes.id');

        return $recentRecipeIds->diff($favoriteIds)->values();
    }
}
