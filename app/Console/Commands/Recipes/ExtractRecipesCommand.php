<?php

namespace App\Console\Commands\Recipes;

use App\Models\Recipe;
use App\Models\RecipeTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Meilisearch\Client;

class ExtractRecipesCommand extends Command
{
    protected $signature = 'recipes:extract
                            {--dry-run : Show what would be created without writing to the database}
                            {--limit= : Limit the number of recipes to process}
                            {--ids= : Extract specific meals by ID (comma-separated, e.g. --ids=1,5,23)}
                            {--fresh : Clear all recipes before extracting}';

    protected $description = 'Extract unique recipes from meals into the recipes master table, using Meilisearch hybrid search to deduplicate';

    private int $created = 0;

    private int $matched = 0;

    private int $skipped = 0;

    private int $translationsCreated = 0;

    private int $mealsLinked = 0;

    private int $missingEnglish = 0;

    private array $duplicateSlugs = [];

    public function handle(Client $client): int
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $ids = $this->option('ids')
            ? array_map('intval', explode(',', $this->option('ids')))
            : null;

        if ($dryRun) {
            $this->warn('🔍 Dry run mode — no data will be written.');
        }

        if ($ids) {
            $this->info('🎯 Extracting specific meal IDs: '.implode(', ', $ids));
        } elseif ($limit) {
            $this->info("🧪 Limiting to {$limit} recipe groups.");
        }

        if (! $dryRun && $this->option('fresh')) {
            if (! $this->confirm('This will delete all existing recipes and unlink meals. Continue?')) {
                return Command::SUCCESS;
            }

            DB::table('meals')->update(['recipe_id' => null]);
            Schema::disableForeignKeyConstraints();
            RecipeTranslation::truncate();
            Recipe::truncate();
            Schema::enableForeignKeyConstraints();

            $this->info('🗑️  Cleared all recipes.');
        }

        $index = $client->index('recipes');

        $uniqueNames = $ids
            ? $this->getUniqueMealNamesByIds($ids)
            : $this->getUniqueMealNames($limit);

        $this->info("Found {$uniqueNames->count()} unique meal names to process.");

        $bar = $this->output->createProgressBar($uniqueNames->count());
        $bar->start();

        foreach ($uniqueNames as $canonicalName) {
            $bar->advance();

            $variants = $this->fetchVariants($canonicalName);

            if ($variants->isEmpty()) {
                $this->skipped++;

                continue;
            }

            $result = $this->buildRecipeData($canonicalName, $variants);

            if ($result === null) {
                $this->skipped++;

                continue;
            }

            // Search Meilisearch for an existing similar recipe
            $existingRecipe = $this->findSimilarRecipe($index, $result['recipe']['name']);

            if ($existingRecipe) {
                // Link meals to the existing recipe
                if (! $dryRun) {
                    $this->linkMealsToRecipe($canonicalName, $existingRecipe->id);
                }

                $this->matched++;

                continue;
            }

            if ($dryRun) {
                $this->created++;

                continue;
            }

            // Create new recipe
            $recipe = Recipe::create($result['recipe']);
            $this->created++;

            foreach ($result['translations'] as $translation) {
                $recipe->translations()->create($translation);
                $this->translationsCreated++;
            }

            // Immediately index in Meilisearch so subsequent meals can find it
            $recipe->loadMissing('translations');
            $index->addDocuments([$recipe->toSearchableArray()]);

            // Link meals to the new recipe
            $this->linkMealsToRecipe($canonicalName, $recipe->id);
        }

        $bar->finish();
        $this->newLine(2);

        $this->printSummary($dryRun, $uniqueNames->count());

        return Command::SUCCESS;
    }

    private function getUniqueMealNamesByIds(array $ids)
    {
        return DB::table('meals')
            ->whereIn('id', $ids)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->select(DB::raw('LOWER(TRIM(name)) as canonical_name'))
            ->distinct()
            ->orderBy('canonical_name')
            ->pluck('canonical_name');
    }

    private function getUniqueMealNames(?int $limit)
    {
        $query = DB::table('meals')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->select(DB::raw('LOWER(TRIM(name)) as canonical_name'))
            ->distinct()
            ->orderBy('canonical_name');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->pluck('canonical_name');
    }

    private function fetchVariants(string $canonicalName)
    {
        return DB::table('meals')
            ->join('meal_plans', 'meals.meal_plan_id', '=', 'meal_plans.id')
            ->join('plans', 'meal_plans.plan_id', '=', 'plans.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->select([
                'meals.name',
                'meals.description',
                'meals.ingredients',
                'meals.instructions',
                'meals.prep_time_minutes',
                'meals.cook_time_minutes',
                'meals.difficulty',
                'meals.servings',
                'meals.calories',
                'meals.protein_g',
                'meals.carbs_g',
                'meals.fat_g',
                'meals.fiber_g',
                'meals.sugar_g',
                'meals.tags',
                'meals.allergens',
                'meals.primary_protein',
                'meals.cuisine',
                'meals.type',
                'meals.image',
                'meals.image_full',
                'meals.image_isolated',
                'users.locale',
            ])
            ->where(DB::raw('LOWER(TRIM(meals.name))'), $canonicalName)
            ->limit(50) // cap variants to avoid memory issues
            ->get();
    }

    private function buildRecipeData(string $key, $variants): ?array
    {
        $english = $this->bestVariantForLocale($variants, 'en');
        $german = $this->bestVariantForLocale($variants, 'de');

        // English preferred; fall back to best available variant for data
        $best = $english ?? $german ?? $variants->first();
        $hasEnglish = $english !== null;

        // Canonical name is always from the English variant when available,
        // otherwise use the non-English name (it will be stored as a translation
        // and flagged for enrichment)
        $canonicalName = trim(($english ?? $best)->name);

        if (! $hasEnglish) {
            $this->missingEnglish++;
        }

        $slug = Str::slug($canonicalName);

        if (empty($slug)) {
            return null;
        }

        $slug = $this->uniqueSlug($slug);

        return [
            'recipe' => [
                'name' => $canonicalName,
                'slug' => $slug,
                'description' => $best->description,
                'ingredients' => $this->decodeJson($best->ingredients),
                'instructions' => $this->decodeJson($best->instructions),
                'prep_time_minutes' => $best->prep_time_minutes,
                'cook_time_minutes' => $best->cook_time_minutes,
                'difficulty' => $best->difficulty,
                'servings' => $best->servings ?? 1,
                'calories' => $best->calories,
                'protein_g' => $best->protein_g,
                'carbs_g' => $best->carbs_g,
                'fat_g' => $best->fat_g,
                'fiber_g' => $best->fiber_g,
                'sugar_g' => $best->sugar_g,
                'tags' => $this->mergeJsonField($variants, 'tags'),
                'allergens' => $this->mergeJsonField($variants, 'allergens'),
                'primary_protein' => $best->primary_protein,
                'cuisine' => $best->cuisine,
                'meal_types' => $variants->pluck('type')->filter()->unique()->values()->toArray() ?: null,
                'image' => $variants->whereNotNull('image')->first()?->image,
                'image_full' => $variants->whereNotNull('image_full')->first()?->image_full,
                'image_isolated' => $variants->whereNotNull('image_isolated')->first()?->image_isolated,
                'is_verified' => false,
                'needs_translation' => ! $hasEnglish,
            ],
            'translations' => $this->buildTranslations($variants, $canonicalName, $hasEnglish, $german),
        ];
    }

    private function buildTranslations($variants, string $canonicalName, bool $hasEnglish, $german): array
    {
        $translations = [];

        // English aliases (only when we have English content)
        if ($hasEnglish) {
            $englishAliases = $this->collectAliases($variants, 'en', $canonicalName);

            if (! empty($englishAliases)) {
                $translations[] = [
                    'locale' => 'en',
                    'name' => $canonicalName,
                    'slug' => Str::slug($canonicalName),
                    'aliases' => $englishAliases,
                ];
            }
        }

        // German translation — when no English exists, the canonical name IS German,
        // so store a German translation pointing back to it for proper localization
        if ($german) {
            $germanNames = $variants
                ->where('locale', 'de')
                ->pluck('name')
                ->map(fn ($n) => trim($n))
                ->unique()
                ->values();

            $germanName = $germanNames->first() ?? $canonicalName;
            $germanAliases = $germanNames
                ->filter(fn ($n) => Str::lower($n) !== Str::lower($germanName))
                ->values()
                ->toArray();

            $translations[] = [
                'locale' => 'de',
                'name' => $germanName,
                'slug' => Str::slug($germanName),
                'aliases' => ! empty($germanAliases) ? $germanAliases : null,
                'description' => $german->description,
                'instructions' => $this->decodeJson($german->instructions),
            ];
        }

        return $translations;
    }

    private function findSimilarRecipe($index, string $name): ?Recipe
    {
        try {
            $results = $index->search($name, [
                'hybrid' => [
                    'semanticRatio' => 0.75,
                    'embedder' => 'default',
                ],
                'limit' => 1,
            ]);

            $hits = $results->getHits();

            if (empty($hits)) {
                return null;
            }

            $topHit = $hits[0];

            // Use ranking score if available, otherwise fall back to semantic score
            $score = $topHit['_rankingScore'] ?? 0;

            if ($score < 0.85) {
                return null;
            }

            return Recipe::find($topHit['id']);
        } catch (\Exception $e) {
            // If Meilisearch is unavailable or index doesn't exist, skip similarity check
            return null;
        }
    }

    private function linkMealsToRecipe(string $canonicalName, int $recipeId): void
    {
        $updated = DB::table('meals')
            ->whereNull('recipe_id')
            ->where(DB::raw('LOWER(TRIM(name))'), $canonicalName)
            ->update(['recipe_id' => $recipeId]);

        $this->mealsLinked += $updated;
    }

    private function bestVariantForLocale($variants, string $locale)
    {
        return $variants
            ->where('locale', $locale)
            ->sortByDesc(fn ($v) => strlen($v->description ?? ''))
            ->first();
    }

    private function collectAliases($variants, string $locale, string $canonicalName): array
    {
        return $variants
            ->where('locale', $locale)
            ->pluck('name')
            ->map(fn ($n) => trim($n))
            ->filter(fn ($n) => Str::lower($n) !== Str::lower($canonicalName))
            ->unique()
            ->values()
            ->toArray();
    }

    private function uniqueSlug(string $slug): string
    {
        if (isset($this->duplicateSlugs[$slug])) {
            $this->duplicateSlugs[$slug]++;

            return $slug.'-'.$this->duplicateSlugs[$slug];
        }

        $this->duplicateSlugs[$slug] = 0;

        return $slug;
    }

    private function decodeJson(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            return json_decode($value, true);
        }

        return null;
    }

    private function mergeJsonField($variants, string $field): ?array
    {
        $merged = $variants
            ->pluck($field)
            ->map(fn ($v) => is_string($v) ? json_decode($v, true) : $v)
            ->filter()
            ->flatten()
            ->map(fn ($v) => is_string($v) ? trim($v) : $v)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return ! empty($merged) ? $merged : null;
    }

    private function printSummary(bool $dryRun, int $total): void
    {
        $label = fn ($v) => $dryRun ? '0 (dry run)' : $v;

        $this->info('✅ Done!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Unique meal names found', $total],
                ['Recipes created', $label($this->created)],
                ['Matched to existing recipe', $label($this->matched)],
                ['Translations created', $label($this->translationsCreated)],
                ['Meals linked to recipes', $label($this->mealsLinked)],
                ['Missing English content', $this->missingEnglish],
                ['Skipped', $this->skipped],
            ]
        );

        if ($this->missingEnglish > 0) {
            $this->warn("⚠️  {$this->missingEnglish} recipes have no English content.");
        }
    }
}
