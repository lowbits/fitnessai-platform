<?php

namespace App\Console\Commands\Recipes;

use App\Ai\Agents\DietaryClassifierAgent;
use App\Models\Recipe;
use Illuminate\Console\Command;
use Throwable;

class EnhanceDietaryTagsCommand extends Command
{
    protected $signature = 'recipes:enhance-dietary-tags {--dry-run : Show what would change without writing}';

    protected $description = 'Use the cheap LLM to backfill cascading dietary tags on existing recipes.';

    private const DIET_TAGS = ['vegan', 'vegetarian', 'pescatarian', 'omnivore'];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;
        $failed = 0;

        Recipe::query()->chunkById(50, function ($recipes) use ($dryRun, &$updated, &$failed) {
            foreach ($recipes as $recipe) {
                try {
                    $cascade = $this->classify($recipe);
                } catch (Throwable $e) {
                    $failed++;
                    $this->warn("Recipe #{$recipe->id}: ".$e->getMessage());

                    continue;
                }

                $other = array_diff($recipe->tags ?? [], self::DIET_TAGS);
                $next = array_values(array_unique([...$other, ...$cascade]));

                if ($next === ($recipe->tags ?? [])) {
                    continue;
                }

                $this->line("#{$recipe->id} {$recipe->name}: ".implode(',', $cascade));

                if (! $dryRun) {
                    $recipe->update(['tags' => $next]);
                }
                $updated++;
            }
        });

        $this->info(($dryRun ? '[dry-run] ' : '')."Updated {$updated} recipes. Failed {$failed}.");

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function classify(Recipe $recipe): array
    {
        $ingredients = collect($recipe->ingredients ?? [])
            ->pluck('name')
            ->filter()
            ->implode(', ');

        $prompt = "Name: {$recipe->name}\nIngredients: {$ingredients}";
        $response = (string) (new DietaryClassifierAgent)->prompt($prompt);

        $tags = array_values(array_filter(array_map(
            fn (string $t) => mb_strtolower(trim($t)),
            explode(',', $response)
        )));

        $valid = array_intersect($tags, self::DIET_TAGS);

        return empty($valid) ? ['omnivore'] : array_values($valid);
    }
}
