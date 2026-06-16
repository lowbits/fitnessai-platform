<?php

namespace App\Ai\Tools;

use App\Enums\Cuisine;
use App\Enums\HeroVeg;
use App\Enums\MealFormat;
use App\Enums\PrimaryProtein;
use App\Models\Meal;
use App\Models\MealPlan;
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
                    ->description('Meal type.')
                    ->enum(['breakfast', 'lunch', 'snack', 'dinner'])
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
                        ->description('Quantity (e.g. "200", "1").')
                        ->required(),
                    'unit' => $schema->string()
                        ->description('Unit of measurement (e.g. "g", "ml", "piece").')
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
                    ->description('Descriptive tags (e.g. "high-protein", "quick", "post-workout").'),

                'allergens' => $schema->array()
                    ->items($schema->string())
                    ->description('Allergens present in this meal (e.g. "dairy", "gluten", "nuts").'),

                'primary_protein' => $schema->string()
                    ->description('Dominant protein source. Pick the single category that carries the protein. Use "mixed" only when there is genuinely no hero protein.')
                    ->enum(array_column(PrimaryProtein::cases(), 'value'))
                    ->required(),

                'cuisine' => $schema->string()
                    ->description('Cuisine the meal belongs to. Use "mixed" only when no identifiable cuisine.')
                    ->enum(array_column(Cuisine::cases(), 'value'))
                    ->required(),

                'format' => $schema->string()
                    ->description('Visual format on the plate. bowl/pasta/noodles for Asian/wrap/sandwich/soup/salad/curry/bake/grill/sheet_pan/stir_fry/omelet/porridge/pancake/toast/yogurt_bowl/smoothie/pizza. Use mixed only if none fits.')
                    ->enum(array_column(MealFormat::cases(), 'value'))
                    ->required(),

                'hero_veg' => $schema->string()
                    ->description('Dominant non-starch vegetable. Carb bases (potato, rice, pasta) are NOT hero_veg. Use "none" if the meal has no vegetable, "mixed" if several share the spotlight.')
                    ->enum(array_column(HeroVeg::cases(), 'value'))
                    ->required(),
            ])->withoutAdditionalProperties())
                ->description('Array of meals for the day, one per requested meal type.')
                ->required(),
        ];
    }

    private function saveMeals(array $meals): void
    {
        foreach ($meals as $meal) {
            Meal::create([
                'meal_plan_id' => $this->mealPlan->id,
                'type' => $meal['type'],
                'name' => $meal['name'],
                'description' => $meal['description'] ?? null,
                'calories' => $meal['calories'],
                'protein_g' => $meal['protein_g'],
                'carbs_g' => $meal['carbs_g'],
                'fat_g' => $meal['fat_g'],
                'fiber_g' => $meal['fiber_g'] ?? null,
                'sugar_g' => $meal['sugar_g'] ?? null,
                'ingredients' => $meal['ingredients'] ?? [],
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
}
