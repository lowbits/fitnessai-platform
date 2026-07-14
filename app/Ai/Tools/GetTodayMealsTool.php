<?php

namespace App\Ai\Tools;

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Returns the user's meals for today so they can pick which one to replace.
 * The app renders the result as selectable meal cards; the chosen meal's id
 * then flows into propose_meal_alternatives.
 */
class GetTodayMealsTool implements Tool
{
    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Returns the user\'s replaceable meals for today. If the user named the slot (e.g. "mein Mittag" → "lunch"), pass type to get just that meal; otherwise omit type to return all so they can pick. The app renders the meals as selectable cards.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->description('The meal slot the user named — one of "breakfast", "lunch", "dinner", "snack". Omit if they did not say which meal.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        // Meals are keyed by day_number relative to the plan's start date —
        // the same resolution the /plan/day endpoint uses (PlanController).
        $plan = $this->user->plans()->where('status', 'active')->first();
        $dayNumber = $plan ? (int) $plan->start_date->diffInDays(today()) + 1 : null;

        $mealPlan = $plan
            ? MealPlan::with('meals.recipe')
                ->where('plan_id', $plan->id)
                ->where('day_number', $dayNumber)
                ->first()
            : null;

        $type = filled($request['type'] ?? null) ? mb_strtolower(trim((string) $request['type'])) : null;

        $allMeals = $mealPlan?->meals ?? collect();
        // Only meals that can still be replaced — an eaten (completed) meal is locked in.
        $replaceable = $allMeals->reject(fn (Meal $meal) => $meal->isCompleted());

        if ($type !== null) {
            $replaceable = $replaceable->filter(fn (Meal $meal) => mb_strtolower($meal->type) === $type)->values();
        }

        Log::debug('[Coach][GetTodayMeals] Built meals', [
            'user_id' => $this->user->id,
            'plan_id' => $plan?->id,
            'day_number' => $dayNumber,
            'total_meals' => $allMeals->count(),
            'replaceable_meals' => $replaceable->count(),
        ]);

        // Nothing to offer — signal Mona to explain rather than render an empty picker.
        if ($replaceable->isEmpty()) {
            if ($type !== null) {
                $eaten = $allMeals->contains(fn (Meal $meal) => mb_strtolower($meal->type) === $type && $meal->isCompleted());

                return json_encode([
                    'error' => $eaten ? 'meal_already_eaten' : 'no_replaceable_meals',
                    'message' => $eaten
                        ? "The {$type} has already been eaten and can no longer be replaced."
                        : "There is no {$type} planned for today to replace.",
                ]);
            }

            return json_encode([
                'error' => 'no_replaceable_meals',
                'message' => $allMeals->isNotEmpty()
                    ? 'All of today\'s meals have already been eaten, so there is nothing left to replace today.'
                    : 'There are no meals planned for today to replace.',
            ]);
        }

        $meals = $replaceable
            ->map(fn (Meal $meal) => [
                'meal_id' => $meal->id,
                'name' => $meal->name,
                'slot' => $meal->type,
                'kcal' => (int) $meal->calories,
                'protein_g' => (int) $meal->protein_g,
                'carbs_g' => (int) $meal->carbs_g,
                'fat_g' => (int) $meal->fat_g,
                'image_url' => $meal->recipe?->thumbnail_url,
            ])
            ->values()
            ->all();

        // Named slot → the meal is resolved here; hand it straight to
        // propose_meal_alternatives (no picker). Only an unspecified slot
        // renders the select_meal picker for the user to choose.
        if ($type !== null) {
            return json_encode([
                'resolved' => true,
                'meals' => $meals,
            ]);
        }

        return json_encode([
            'widget' => 'select_meal',
            'requires_input' => true,
            'data' => ['meals' => $meals],
        ]);
    }
}
