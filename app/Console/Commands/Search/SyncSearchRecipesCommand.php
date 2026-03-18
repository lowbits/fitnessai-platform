<?php

namespace App\Console\Commands\Search;

use App\Models\Recipe;
use Illuminate\Console\Command;
use Meilisearch\Client;

class SyncSearchRecipesCommand extends Command
{
    protected $signature = 'recipes:sync-search
                            {--fresh : Delete all documents before importing}';

    protected $description = 'Sync all recipes to Meilisearch';

    public function handle(Client $client): int
    {
        $index = $client->index('recipes');

        if ($this->option('fresh')) {
            $this->info('Clearing Meilisearch index...');
            $index->deleteAllDocuments();
        }

        $total = Recipe::count();

        if ($total === 0) {
            $this->warn('No recipes to sync.');

            return Command::SUCCESS;
        }

        $this->info("Syncing {$total} recipes...");
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
        $this->info("✅ {$total} recipes synced.");

        return Command::SUCCESS;
    }
}
