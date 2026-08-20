<?php

namespace App\Ai\Tools;

use App\Ai\Support\MealDrafter;
use App\Ai\Tools\Concerns\InteractsWithPlan;
use App\Ai\Tools\Support\DailyBudget;
use App\Ai\Tools\Support\ToolResult;
use App\Models\User;
use App\Services\Recipe\MealAlternatives;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Adds a NEW suggested meal to today's plan when the day doesn't already have
 * it — an extra snack, an empty main slot, or filling the user's open calories.
 * Changing an existing meal is propose_meal_alternatives' job, not this one.
 */
class AddMealTool implements Tool
{
    use InteractsWithPlan;

    private const FILL_FLOOR_KCAL = 100;

    private const MAIN_SLOTS = ['breakfast', 'lunch', 'dinner'];

    /** Fallback share of the daily goal per slot when the model gives no estimate. */
    private const SLOT_SHARE = ['breakfast' => 0.275, 'lunch' => 0.325, 'dinner' => 0.275, 'snack' => 0.125];

    public function __construct(
        private readonly User $user,
        private readonly MealDrafter $drafter,
        private readonly MealAlternatives $alternatives,
    ) {}

    public function description(): Stringable|string
    {
        return 'Adds a NEW suggested meal to today when the day does not already have it: an extra snack ("füge einen Snack hinzu"), an empty main slot, or filling the open calories ("fülle meine offenen Kalorien"). Pass type (breakfast|lunch|dinner|snack, default snack), optional request (what they want), fill_remaining=true to size it to the open calories, approx_kcal for your estimate of a named dish, and confirmed=true only after the user confirms going over their goal. To CHANGE an existing meal, use propose_meal_alternatives instead.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->description('breakfast, lunch, dinner or snack. Defaults to snack.'),
            'request' => $schema->string()
                ->description('What the user wants, if they named it (e.g. "etwas mit Lachs").'),
            'approx_kcal' => $schema->integer()
                ->description('Your kcal estimate for a specific named dish, used for the budget check.'),
            'fill_remaining' => $schema->boolean()
                ->description('True when the user wants to fill their remaining open calories for today.'),
            'confirmed' => $schema->boolean()
                ->description('True only after the user confirmed they want to go over their calorie goal.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $type = strtolower((string) ($request['type'] ?? 'snack'));
        if (! isset(self::SLOT_SHARE[$type])) {
            $type = 'snack';
        }

        $fill = (bool) ($request['fill_remaining'] ?? false);
        $confirmed = (bool) ($request['confirmed'] ?? false);
        $wish = $request['request'] ?? null;

        $mealPlan = $this->todaysMealPlan($this->user);
        if (! $mealPlan) {
            return ToolResult::error('no_active_plan', 'There is no plan for today to add a meal to.');
        }

        if (in_array($type, self::MAIN_SLOTS, true) && $mealPlan->meals->firstWhere('type', $type)) {
            return ToolResult::error(
                'slot_already_planned',
                "There is already a {$type} today — offer to swap it instead of adding a second one.",
                ['slot' => $type],
            );
        }

        $budget = DailyBudget::for($this->user);

        if ($fill) {
            if ($budget->remaining < self::FILL_FLOOR_KCAL) {
                return ToolResult::error(
                    'no_room',
                    'There are barely any open calories left today — tell the user their day is essentially full.',
                    ['remaining' => (int) round($budget->remaining)],
                );
            }
            $targetKcal = (int) round($budget->remaining);
        } else {
            $targetKcal = (int) round($request['approx_kcal'] ?? $budget->goal * self::SLOT_SHARE[$type]);

            if (! $confirmed && $budget->goal > 0 && $targetKcal > $budget->remaining) {
                return ToolResult::error(
                    'budget_exceeded',
                    'That would put the user over their calorie goal for today — tell them the numbers and ask if they still want to add it.',
                    [
                        'goal' => $budget->goal,
                        'eaten' => (int) round($budget->eaten),
                        'remaining' => (int) round($budget->remaining),
                        'would_add' => $targetKcal,
                    ],
                );
            }
        }

        $recipe = $this->drafter->draft($this->user, $type, $targetKcal, $wish);
        if (! $recipe) {
            return ToolResult::error('draft_failed', 'Could not draft a meal right now — ask the user to try again.');
        }

        $meal = $mealPlan->meals()->create([
            'recipe_id' => $recipe->id,
            'type' => $type,
            'name' => $recipe->localizedName($this->user->locale ?? 'en'),
            'calories' => $recipe->calories,
            'protein_g' => $recipe->protein_g,
            'carbs_g' => $recipe->carbs_g,
            'fat_g' => $recipe->fat_g,
            'ingredients' => $recipe->ingredients,
            'servings' => 1,
            'status' => 'generated',
        ]);

        // Reuse the swap widget: the added meal is the "original", with other
        // options as cards, so the client renders and commits it like any swap.
        return ToolResult::widget('meal_alternatives', $this->alternatives->for($this->user, $meal));
    }
}
