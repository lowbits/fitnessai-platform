<?php

namespace App\Ai\Tools;

use App\Ai\Tools\Support\ToolResult;
use App\Models\Meal;
use App\Models\User;
use App\Services\Recipe\MealAlternatives;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Proposes replacement meals for one of the user's meals. The AI passes the
 * meal_id (from get_today_meals or the seeded context); ownership is verified
 * here since the id comes from the model.
 */
class ProposeMealAlternativesTool implements Tool
{
    public function __construct(
        private readonly User $user,
        private readonly MealAlternatives $alternatives,
    ) {}

    public function description(): Stringable|string
    {
        return 'Proposes replacement meals for one of the user\'s meals from existing recipes. Requires meal_id (get it from get_today_meals if you don\'t already have it). Pass what the user asked for as wish — a dish ("chili con carne"), a preference ("more protein", "under 20 minutes", "lighter"), or ingredients they have ("chicken, rice, broccoli"). The app renders the returned cards — do not list them in prose.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'meal_id' => $schema->integer()
                ->description('ID of the meal to replace. Must be one of the user\'s own meals (from get_today_meals or the current context).')
                ->required(),
            'wish' => $schema->string()
                ->description('What the user asked for in the replacement — a specific dish ("chili con carne") or a preference ("more protein", "lighter", "under 20 minutes"). Omit to just show the best-fitting options.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $mealId = (int) ($request['meal_id'] ?? 0);
        $wish = filled($request['wish'] ?? null) ? trim((string) $request['wish']) : null;

        $meal = Meal::with(['mealPlan.plan', 'recipe'])->find($mealId);

        if (! $meal || $meal->mealPlan?->plan?->user_id !== $this->user->id) {
            return ToolResult::error('meal_not_found', 'That meal could not be found.', ['cards' => []]);
        }

        if ($meal->isCompleted()) {
            return ToolResult::error('meal_already_eaten', 'This meal has already been eaten and can no longer be replaced.', ['cards' => []]);
        }

        $data = $this->alternatives->for($this->user, $meal, $wish);

        if ($data['cards'] === []) {
            return ToolResult::error('no_alternatives', 'No matching recipes were found — offer to create one for them instead.', ['cards' => []]);
        }

        return ToolResult::widget('meal_alternatives', $data);
    }
}
