<?php

namespace App\Ai\Tools;

use App\Enums\Allergen;
use App\Enums\Cuisine;
use App\Enums\HeroVeg;
use App\Enums\MealFormat;
use App\Enums\PrimaryProtein;
use App\Enums\Unit;
use App\Services\Recipe\RecipeUpserter;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SaveRecipesTool implements Tool
{
    /** @var list<int> */
    public array $createdIds = [];

    public function __construct(
        private readonly string $locale,
        private readonly string $mealType,
        private readonly bool $dryRun = false,
    ) {}

    public function description(): Stringable|string
    {
        return 'Saves a batch of generated recipes for a single (locale, meal_type) slot. Call exactly once with all recipes.';
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            $recipes = $request['recipes'] ?? [];
            $upserter = app(RecipeUpserter::class);
            $created = 0;
            $duped = 0;

            foreach ($recipes as $data) {
                $before = $this->createdIds;
                $recipe = $this->dryRun
                    ? null
                    : $upserter->upsert($data, $this->locale, $this->mealType);

                if ($recipe && $recipe->wasRecentlyCreated) {
                    $this->createdIds[] = $recipe->id;
                    $created++;
                } else {
                    $duped++;
                }
            }

            return json_encode(['success' => true, 'created' => $created, 'duped' => $duped]);
        } catch (Exception $e) {
            Log::error('[SaveRecipesTool] Failed', ['error' => $e->getMessage()]);

            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'recipes' => $schema->array()->items($schema->object([
                'name' => $schema->string()->required(),
                'description' => $schema->string(),
                'calories' => $schema->number()->required(),
                'protein_g' => $schema->number()->required(),
                'carbs_g' => $schema->number()->required(),
                'fat_g' => $schema->number()->required(),
                'fiber_g' => $schema->number(),
                'sugar_g' => $schema->number(),
                'ingredients' => $schema->array()->items($schema->object([
                    'name' => $schema->string()->required(),
                    'amount' => $schema->string()->required(),
                    'unit' => $schema->string()->enum(array_column(Unit::cases(), 'value'))->required(),
                ])->withoutAdditionalProperties())->required(),
                'instructions' => $schema->array()->items($schema->string())->required(),
                'prep_time_minutes' => $schema->number(),
                'cook_time_minutes' => $schema->number(),
                'difficulty' => $schema->string()->enum(['Easy', 'Medium', 'Hard']),
                'tags' => $schema->array()->items($schema->string())->required(),
                'allergens' => $schema->array()->items($schema->string()->enum(array_column(Allergen::cases(), 'value'))),
                'primary_protein' => $schema->string()->enum(array_column(PrimaryProtein::cases(), 'value'))->required(),
                'cuisine' => $schema->string()->enum(array_column(Cuisine::cases(), 'value'))->required(),
                'format' => $schema->string()->enum(array_column(MealFormat::cases(), 'value'))->required(),
                'hero_veg' => $schema->string()->enum(array_column(HeroVeg::cases(), 'value')),
            ])->withoutAdditionalProperties())->required(),
        ];
    }
}
