<?php

namespace App\Ai\Support;

use App\Ai\Agents\RecipeSeederAgent;
use App\Enums\DietaryPreference;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Drafts a single suggested recipe for a user via the on-demand generator.
 * Wrapping RecipeSeederAgent here keeps the LLM call behind one seam, so tools
 * that draft meals stay thin and are testable with a fake drafter.
 */
class MealDrafter
{
    public function draft(User $user, string $mealType, int $targetKcal, ?string $request = null): ?Recipe
    {
        $diet = $user->profile?->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $agent = new RecipeSeederAgent(
            diet: $diet->value,
            mealType: $mealType,
            locale: $user->locale ?? 'en',
            count: 1,
            request: $request,
            targetKcal: $targetKcal,
        );

        try {
            $agent->prompt('Generate the recipe now.');
        } catch (Throwable $e) {
            Log::error('[Coach][MealDrafter] Generator failed', [
                'meal_type' => $mealType,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        $id = end($agent->saveTool->upsertedIds) ?: null;

        return $id ? Recipe::find($id) : null;
    }
}
