<?php

namespace App\Console\Commands\Search;

use App\Models\Exercise;
use Illuminate\Console\Command;
use Meilisearch\Client;

class SyncSearchExercisesCommand extends Command
{
    protected $signature = 'exercises:sync-search
                            {--fresh : Delete all documents before importing}';

    protected $description = 'Sync all exercises to Meilisearch';

    public function handle(Client $client): int
    {
        $index = $client->index('exercises');

        if ($this->option('fresh')) {
            $this->info('Clearing Meilisearch index...');
            $index->deleteAllDocuments();
        }

        $total = Exercise::count();

        if ($total === 0) {
            $this->warn('No exercises to sync.');

            return Command::SUCCESS;
        }

        $this->info("Syncing {$total} exercises...");
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
        $this->info("✅ {$total} exercises synced.");

        return Command::SUCCESS;
    }
}
