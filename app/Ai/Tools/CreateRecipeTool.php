<?php

namespace App\Ai\Tools;

use App\Ai\Agents\RecipeSeederAgent;
use App\Enums\DietaryPreference;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use App\Services\Recipe\MealAlternativeCard;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use Throwable;

/**
 * Generates a brand-new recipe on demand when search can't satisfy the user —
 * a named dish we don't have ("chili con carne") or a meal from ingredients they
 * have at home ("chicken, rice, broccoli"). The dish is generated to fit the
 * target meal's slot + calories, persisted (dedup + async image), and returned
 * as a meal_alternatives card so the swap flow is identical to search results.
 *
 * This is the slow path (a nested generation call), so Mona only reaches for it
 * after the user confirms — never speculatively.
 */
class CreateRecipeTool implements Tool
{
    public function __construct(private readonly User $user) {}

    public function description(): Stringable|string
    {
        return 'Generates a new recipe the user asked for and returns it as a swap card. Use ONLY after the user confirmed they want it created (e.g. a dish we do not have like "chili con carne", or a meal from ingredients they have at home). Requires meal_id (the meal it will replace) and request (the dish name or the ingredients). This takes a few seconds.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'meal_id' => $schema->integer()
                ->description('ID of the meal this new recipe would replace (from get_today_meals or the current context). The new recipe is sized to fit its slot and calories.')
                ->required(),
            'request' => $schema->string()
                ->description('What to create — a specific dish the user named ("Chili con Carne") or the ingredients they have at home ("chicken, rice, broccoli").')
                ->required(),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $mealId = (int) ($request['meal_id'] ?? 0);
        $wish = trim((string) ($request['request'] ?? ''));

        if ($wish === '') {
            return json_encode(['error' => 'missing_request', 'cards' => []]);
        }

        $meal = Meal::with(['mealPlan.plan', 'recipe'])->find($mealId);

        if (! $meal || $meal->mealPlan?->plan?->user_id !== $this->user->id) {
            Log::warning('[Coach][CreateRecipe] Meal not found or not owned', [
                'meal_id' => $mealId,
                'user_id' => $this->user->id,
            ]);

            return json_encode(['error' => 'meal_not_found', 'cards' => []]);
        }

        if ($meal->isCompleted()) {
            return json_encode([
                'error' => 'meal_already_eaten',
                'message' => 'This meal has already been eaten and can no longer be replaced.',
                'cards' => [],
            ]);
        }

        $recipe = $this->generate($meal, $wish);

        if (! $recipe) {
            Log::warning('[Coach][CreateRecipe] Generation produced no recipe', [
                'meal_id' => $meal->id,
                'request' => $wish,
            ]);

            return json_encode([
                'error' => 'generation_failed',
                'message' => 'I could not create that recipe right now.',
                'cards' => [],
            ]);
        }

        $locale = $this->user->locale ?? 'en';

        Log::debug('[Coach][CreateRecipe] Recipe ready', [
            'meal_id' => $meal->id,
            'request' => $wish,
            'recipe_id' => $recipe->id,
        ]);

        return json_encode([
            'widget' => 'meal_alternatives',
            'requires_input' => true,
            'data' => [
                'meal_id' => $meal->id,
                'slot' => $meal->type,
                'original' => [
                    'name' => $meal->name,
                    'calories' => (int) $meal->calories,
                    'protein_g' => (int) $meal->protein_g,
                    'image_url' => $meal->recipe?->thumbnail_url,
                ],
                'cards' => [MealAlternativeCard::make($recipe, $meal, $locale)],
            ],
        ]);
    }

    /**
     * Run the on-demand generator for a single dish sized to the meal's slot,
     * then load the persisted recipe (freshly created or matched via dedup).
     */
    private function generate(Meal $meal, string $wish): ?Recipe
    {
        $diet = $this->user->profile?->resolveDietaryPreference();
        $diet = $diet instanceof DietaryPreference ? $diet : DietaryPreference::OMNIVORE;

        $agent = new RecipeSeederAgent(
            diet: $diet->value,
            mealType: $meal->type,
            locale: $this->user->locale ?? 'en',
            count: 1,
            request: $wish,
            targetKcal: (int) $meal->calories,
        );

        try {
            $agent->prompt('Generate the recipe now.');
        } catch (Throwable $e) {
            Log::error('[Coach][CreateRecipe] Generator failed', [
                'request' => $wish,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        $id = end($agent->saveTool->upsertedIds) ?: null;

        return $id ? Recipe::find($id) : null;
    }
}
