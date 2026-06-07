<?php

namespace App\Console\Commands\Recipes;

use App\Jobs\GenerateRecipeImage;
use App\Models\Recipe;
use Illuminate\Console\Command;

class GenerateRecipeImages extends Command
{
    protected $signature = 'recipes:generate-images
                            {--ids= : Generate images for specific recipe IDs (comma-separated)}
                            {--limit= : Limit the number of recipes to process}
                            {--force : Regenerate images even if they already exist}
                            {--sync : Run synchronously instead of dispatching to queue}
                            {--dry-run : Preview which recipes would be processed}';

    protected $description = 'Generate AI food images for recipes and upload to R2';

    public function handle(): int
    {
        $query = Recipe::query();

        if ($ids = $this->option('ids')) {
            $query->whereIn('id', array_map('intval', explode(',', $ids)));
        }

        if (! $this->option('force')) {
            $query->whereNull('image_full');
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $recipes = $query->get();

        if ($recipes->isEmpty()) {
            $this->info('No recipes to process.');

            return Command::SUCCESS;
        }

        $this->info("Found {$recipes->count()} recipe(s) to process.");

        if ($this->option('dry-run')) {
            $this->table(
                ['ID', 'Name', 'Has Image'],
                $recipes->map(fn (Recipe $r) => [
                    $r->id,
                    $r->name,
                    $r->image_full ? 'Yes' : 'No',
                ])->toArray(),
            );

            return Command::SUCCESS;
        }

        foreach ($recipes as $recipe) {
            if ($this->option('sync')) {
                $this->line("Generating #{$recipe->id}: {$recipe->name}...");
                GenerateRecipeImage::dispatchSync($recipe);
                $this->info('  ✓ Done');
            } else {
                GenerateRecipeImage::dispatch($recipe);
                $this->line("Dispatched #{$recipe->id}: {$recipe->name}");
            }
        }

        if ($this->option('sync')) {
            $this->info("Generated {$recipes->count()} image(s).");
        } else {
            $this->info("Dispatched {$recipes->count()} job(s) to queue. Run multiple queue workers for parallelism.");
        }

        return Command::SUCCESS;
    }
}
