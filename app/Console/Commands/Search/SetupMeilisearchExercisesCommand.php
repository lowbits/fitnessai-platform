<?php

namespace App\Console\Commands\Search;

use App\Models\Exercise;
use Illuminate\Console\Command;
use Meilisearch\Client;

class SetupMeilisearchExercisesCommand extends Command
{
    protected $signature = 'exercises:setup-search
                            {--import : Import all exercises after configuring the index}
                            {--reset : Delete the index and recreate it from scratch}';

    protected $description = <<<'DESC'
    Configure the Meilisearch "exercises" index with filterable, searchable,
    and sortable attributes, plus OpenAI embeddings for hybrid search.

    Usage:
      First-time setup:    php artisan exercises:setup-search --import
      Reconfigure index:   php artisan exercises:setup-search
      Full reset:          php artisan exercises:setup-search --reset --import
    DESC;

    public function handle(Client $client): int
    {
        if ($this->option('reset')) {
            if (! $this->confirm('This will delete the entire exercises index. Continue?')) {
                return Command::SUCCESS;
            }

            $client->deleteIndex('exercises');
            $this->warn('🗑️  Index deleted.');
        }

        $index = $client->index('exercises');

        $this->configureFilterableAttributes($index);
        $this->configureSearchableAttributes($index);
        $this->configureSortableAttributes($index);
        $this->configureEmbedders($index);

        $this->info('✅ Index configured.');

        if ($this->option('import')) {
            $this->importExercises($index);
        } else {
            $this->newLine();
            $this->line('To import exercises, run:');
            $this->line('  php artisan exercises:setup-search --import');
            $this->line('  php artisan exercises:sync-search');
        }

        return Command::SUCCESS;
    }

    private function configureFilterableAttributes($index): void
    {
        $this->info('Setting filterable attributes...');

        $index->updateFilterableAttributes([
            'type',
            'movement_pattern',
            'mechanic',
            'body_region',
            'difficulty',
            'primary_muscles',
            'secondary_muscles',
            'equipment',
            'training_places',
            'event_tags',
            'is_verified',
        ]);
    }

    private function configureSearchableAttributes($index): void
    {
        $this->info('Setting searchable attributes (ordered by priority)...');

        $index->updateSearchableAttributes([
            'all_names',       // 1st: name + translations + aliases
            'name',            // 2nd: canonical English name
            'search_text',     // 3rd: rich text for broad matching
            'primary_muscles', // 4th: muscle group keywords
            'equipment',       // 5th: equipment keywords
            'type',            // 6th: exercise type
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

    private function importExercises($index): void
    {
        $total = Exercise::count();

        if ($total === 0) {
            $this->warn('No exercises found to import.');

            return;
        }

        $this->info("Importing {$total} exercises...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        Exercise::with('translations')
            ->chunk(100, function ($exercises) use ($index, $bar) {
                $index->addDocuments(
                    $exercises->map->toSearchableArray()->toArray()
                );

                $bar->advance($exercises->count());
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$total} exercises imported.");
    }
}
