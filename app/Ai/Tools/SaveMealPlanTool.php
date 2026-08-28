<?php

namespace App\Ai\Tools;

use App\Enums\Allergen;
use App\Enums\Cuisine;
use App\Enums\HeroVeg;
use App\Enums\MealFormat;
use App\Enums\PrimaryProtein;
use App\Enums\Unit;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Services\Recipe\RecipeUpserter;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SaveMealPlanTool implements Tool
{
    public function __construct(private readonly MealPlan $mealPlan) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Saves a complete daily meal plan to the database. You MUST call this tool to submit the meal plan.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        try {
            $meals = $request['meals'] ?? [];

            Log::debug('[MealGen][SaveTool] Saving meal plan', [
                'meal_plan_id' => $this->mealPlan->id,
                'meals_count' => count($meals),
                'meal_types' => array_column($meals, 'type'),
            ]);

            $this->saveMeals($meals);
            $totals = $this->calculateTotals($meals);

            $this->mealPlan->update([
                'status' => 'generated',
                'total_calories' => $totals['calories'],
                'total_protein_g' => $totals['protein_g'],
                'total_carbs_g' => $totals['carbs_g'],
                'total_fat_g' => $totals['fat_g'],
            ]);

            Log::info('[MealGen][SaveTool] Saved successfully', [
                'meal_plan_id' => $this->mealPlan->id,
                'meals_count' => count($meals),
                'totals' => $totals,
            ]);

            return json_encode([
                'success' => true,
                'message' => 'Meal plan saved with '.count($meals).' meals.',
            ]);
        } catch (Exception $e) {
            Log::error('[MealGen][SaveTool] Failed to save', [
                'meal_plan_id' => $this->mealPlan->id,
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'meals' => $schema->array()->items($schema->object([
                'type' => $schema->string()
                    ->description('Meal type. "flex" is a no-cook booster (shake/snack) that tops the day up to its calorie target.')
                    ->enum(['breakfast', 'lunch', 'snack', 'dinner', 'flex'])
                    ->required(),

                'name' => $schema->string()
                    ->description('Appetizing meal name in user language.')
                    ->required(),

                'description' => $schema->string()
                    ->description('Brief appetizing description of the meal in user language.'),

                'calories' => $schema->number()
                    ->description('Total calories for this meal.')
                    ->required(),

                'protein_g' => $schema->number()
                    ->description('Protein in grams.')
                    ->required(),

                'carbs_g' => $schema->number()
                    ->description('Carbohydrates in grams.')
                    ->required(),

                'fat_g' => $schema->number()
                    ->description('Fat in grams.')
                    ->required(),

                'fiber_g' => $schema->number()
                    ->description('Fiber in grams.'),

                'sugar_g' => $schema->number()
                    ->description('Sugar in grams.'),

                'ingredients' => $schema->array()->items($schema->object([
                    'name' => $schema->string()
                        ->description('Ingredient name in user language.')
                        ->required(),
                    'amount' => $schema->string()
                        ->description('Quantity (e.g. "200", "1"). Use null/"" for unit=to_taste.')
                        ->required(),
                    'unit' => $schema->string()
                        ->enum(array_column(Unit::cases(), 'value'))
                        ->description('Canonical English unit. NEVER localize — use "piece" not "Stück", "tbsp" not "EL", "pinch" not "Prise".')
                        ->required(),
                ])->withoutAdditionalProperties())
                    ->description('List of ingredients with amounts.'),

                'instructions' => $schema->array()
                    ->items($schema->string())
                    ->description('Step-by-step cooking instructions in user language.'),

                'prep_time_minutes' => $schema->number()
                    ->description('Preparation time in minutes.'),

                'cook_time_minutes' => $schema->number()
                    ->description('Cooking time in minutes.'),

                'difficulty' => $schema->string()
                    ->description('Recipe difficulty level.')
                    ->enum(['Easy', 'Medium', 'Hard']),

                'tags' => $schema->array()
                    ->items($schema->string())
                    ->description('Descriptive tags (e.g. "high-protein", "quick", "post-workout"). MUST include the dietary classification tags this recipe qualifies for, cascading: a vegan recipe includes "vegan", "vegetarian", "pescatarian", "omnivore". A vegetarian recipe includes "vegetarian", "pescatarian", "omnivore". A fish/seafood recipe includes "pescatarian", "omnivore". A meat recipe includes "omnivore" only.'),

                'allergens' => $schema->array()
                    ->items($schema->string()->enum(array_column(Allergen::cases(), 'value')))
                    ->description('EU-14 allergens present in this meal. Use ONLY these canonical lowercase keys. Do not include notes (e.g. "gluten-free possible") — that is not an allergen.'),

                'primary_protein' => $schema->string()
                    ->description('Dominant protein source. Pick the single category that carries the protein. Use "mixed" only when there is genuinely no hero protein.')
                    ->enum(array_column(PrimaryProtein::cases(), 'value'))
                    ->required(),

                'cuisine' => $schema->string()
                    ->description('Cuisine the meal belongs to. Pattern hints: open-faced bread/sandwich with cold cuts or cheese → german. Skillet pasta or gnocchi dishes → italian. Shakshuka, hummus plates, couscous bowls → middle_eastern. Yogurt-and-toppings dishes without a strong cuisine signal → american. Use "mixed" only when there is genuinely no identifiable cuisine.')
                    ->enum(array_column(Cuisine::cases(), 'value'))
                    ->required(),

                'format' => $schema->string()
                    ->description('Visual format on the plate. Pattern hints (the WHOLE-DISH structure, not a single ingredient): eggs-as-main even cooked in sauce → omelet (shakshuka, frittata, scramble). Strained-yogurt or fresh-cheese bowl with fruit/granola toppings → yogurt_bowl. Pasta-shape skillet or sauté → pasta (not stir_fry). Hot grain breakfast (oats, congee) → porridge. Use "mixed" ONLY when truly no format fits — NEVER as a default for ambiguous cases.')
                    ->enum(array_column(MealFormat::cases(), 'value'))
                    ->required(),

                'hero_veg' => $schema->string()
                    ->description('Dominant non-starch vegetable. PICK THE SINGLE most prominent vegetable even when multiple are present — this is a variety axis and "mixed" weakens dedup. Use "mixed" ONLY when 3+ vegetables truly share equal prominence. Use "none" only when the dish has no vegetable at all. Carb bases (potato, rice, pasta) are NEVER hero_veg.')
                    ->enum(array_column(HeroVeg::cases(), 'value'))
                    ->required(),
            ])->withoutAdditionalProperties())
                ->description('Array of meals for the day, one per requested meal type.')
                ->required(),
        ];
    }

    private function saveMeals(array $meals): void
    {
        $user = $this->mealPlan->plan->user;
        $locale = $user->locale ?? 'en';
        $dislikes = $user->profile?->food_dislikes ?? [];
        $upserter = app(RecipeUpserter::class);

        foreach ($meals as $meal) {
            $this->flagDislikeLeaks($meal, $dislikes, $user->id);
            $recipe = $upserter->upsert($meal, $locale, $meal['type']);

            Meal::create([
                'meal_plan_id' => $this->mealPlan->id,
                'recipe_id' => $recipe->id,
                'type' => $meal['type'],
                'name' => $meal['name'],
                'description' => $meal['description'] ?? null,
                'calories' => $meal['calories'],
                'protein_g' => $meal['protein_g'],
                'carbs_g' => $meal['carbs_g'],
                'fat_g' => $meal['fat_g'],
                'fiber_g' => $meal['fiber_g'] ?? null,
                'sugar_g' => $meal['sugar_g'] ?? null,
                'ingredients' => $recipe->ingredients,
                'instructions' => $meal['instructions'] ?? [],
                'prep_time_minutes' => $meal['prep_time_minutes'] ?? null,
                'cook_time_minutes' => $meal['cook_time_minutes'] ?? null,
                'difficulty' => $meal['difficulty'] ?? 'Medium',
                'servings' => 1,
                'tags' => $meal['tags'] ?? [],
                'allergens' => $meal['allergens'] ?? [],
                'primary_protein' => $meal['primary_protein'] ?? null,
                'cuisine' => $meal['cuisine'] ?? null,
                'format' => $meal['format'] ?? null,
                'hero_veg' => $meal['hero_veg'] ?? null,
                'status' => 'generated',
            ]);
        }
    }

    private function calculateTotals(array $meals): array
    {
        return [
            'calories' => array_sum(array_column($meals, 'calories')),
            'protein_g' => array_sum(array_column($meals, 'protein_g')),
            'carbs_g' => array_sum(array_column($meals, 'carbs_g')),
            'fat_g' => array_sum(array_column($meals, 'fat_g')),
        ];
    }

    /**
     * Log when an AI-generated meal contains a disliked ingredient. Case-
     * insensitive substring match in the user's own language — the AI
     * generates in their locale, so a German user's "haselnuss" matches
     * "Haselnuss"/"Haselnüsse" in the generated recipe.
     *
     * @param  list<string>  $dislikes
     */
    private function flagDislikeLeaks(array $meal, array $dislikes, int $userId): void
    {
        if (empty($dislikes)) {
            return;
        }

        $needles = array_filter(array_map(fn (string $d) => mb_strtolower(trim($d)), $dislikes));

        foreach ($meal['ingredients'] ?? [] as $ingredient) {
            $name = mb_strtolower($ingredient['name'] ?? '');
            foreach ($needles as $needle) {
                if (str_contains($name, $needle)) {
                    Log::warning('[MealGen] Disliked ingredient leaked into generated meal', [
                        'meal_name' => $meal['name'] ?? null,
                        'ingredient' => $ingredient['name'] ?? null,
                        'matched_dislike' => $needle,
                        'user_id' => $userId,
                    ]);
                    break;
                }
            }
        }
    }
}
