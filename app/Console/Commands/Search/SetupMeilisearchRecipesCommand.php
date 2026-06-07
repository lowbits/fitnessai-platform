<?php

namespace App\Console\Commands\Search;

use App\Models\Recipe;
use Illuminate\Console\Command;
use Meilisearch\Client;

class SetupMeilisearchRecipesCommand extends Command
{
    protected $signature = 'recipes:setup-search
                            {--import : Import all recipes after configuring the index}
                            {--reset : Delete the index and recreate it from scratch}';

    protected $description = <<<'DESC'
    Configure the Meilisearch "recipes" index with filterable, searchable,
    and sortable attributes, plus OpenAI embeddings for hybrid search.

    Usage:
      First-time setup:    php artisan recipes:setup-search --import
      Reconfigure index:   php artisan recipes:setup-search
      Full reset:          php artisan recipes:setup-search --reset --import
    DESC;

    public function handle(Client $client): int
    {
        if ($this->option('reset')) {
            if (! $this->confirm('This will delete the entire recipes index. Continue?')) {
                return Command::SUCCESS;
            }

            $client->deleteIndex('recipes');
            $this->warn('🗑️  Index deleted.');
        }

        $index = $client->index('recipes');

        $this->configureFilterableAttributes($index);
        $this->configureSearchableAttributes($index);
        $this->configureSortableAttributes($index);
        $this->configureEmbedders($index);

        $this->info('✅ Index configured.');

        if ($this->option('import')) {
            $this->importRecipes($index);
        } else {
            $this->newLine();
            $this->line('To import recipes, run:');
            $this->line('  php artisan recipes:setup-search --import');
            $this->line('  php artisan recipes:sync-search');
        }

        return Command::SUCCESS;
    }

    private function configureFilterableAttributes($index): void
    {
        $this->info('Setting filterable attributes...');

        $index->updateFilterableAttributes([
            'cuisine',
            'primary_protein',
            'difficulty',
            'tags',
            'allergens',
            'meal_types',
            'ingredient_names',
            'total_time_minutes',
            'calories',
            'is_verified',
            'image_full',
        ]);
    }

    private function configureSearchableAttributes($index): void
    {
        $this->info('Setting searchable attributes (ordered by priority)...');

        $index->updateSearchableAttributes([
            'all_names',       // 1st: name + translations + aliases
            'name',            // 2nd: canonical English name
            'search_text',     // 3rd: rich text for broad matching
            'primary_protein', // 4th: protein type
            'cuisine',         // 5th: cuisine type
            'tags',            // 6th: tags
        ]);
    }

    private function configureSortableAttributes($index): void
    {
        $this->info('Setting sortable attributes...');

        $index->updateSortableAttributes([
            'name',
            'difficulty',
        ]);
    }

    private function configureEmbedders($index): void
    {
        $this->info('Configuring OpenAI embedder for hybrid search...');

        $index->updateSettings([
            'embedders' => [
                'default' => [
                    'source' => 'openAi',
                    'apiKey' => config('services.openai.api_key'),
                    'model' => 'text-embedding-3-small',
                    'documentTemplate' => '{{ doc.search_text }}',
                ],
            ],
        ]);
    }

    private function importRecipes($index): void
    {
        $total = Recipe::count();

        if ($total === 0) {
            $this->warn('No recipes found to import.');

            return;
        }

        $this->info("Importing {$total} recipes...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        Recipe::with('translations')
            ->chunk(100, function ($recipes) use ($index, $bar) {
                $index->addDocuments(
                    $recipes->map->toSearchableArray()->toArray()
                );

                $bar->advance($recipes->count());
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$total} recipes imported.");
    }
}
