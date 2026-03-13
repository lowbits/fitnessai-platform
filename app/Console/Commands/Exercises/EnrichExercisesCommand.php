<?php

namespace App\Console\Commands\Exercises;

use App\Jobs\EnrichExerciseJob;
use App\Jobs\EnrichExerciseTranslationsJob;
use App\Models\Exercise;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class EnrichExercisesCommand extends Command
{
    protected $signature = 'exercises:enrich
        {--ids= : Comma-separated exercise IDs to enrich}
        {--limit= : Maximum number of exercises to process}
        {--force : Re-enrich even if fields are already filled}
        {--dry-run : Show what would be processed without dispatching}
        {--locales=de,en : Comma-separated locales for translations}
        {--skip-enrichment : Skip base field enrichment, only do translations}
        {--skip-translations : Skip translations, only do base enrichment}';

    protected $description = 'Dispatch AI enrichment jobs for exercises';

    public function handle(): int
    {
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');
        $skipEnrichment = $this->option('skip-enrichment');
        $skipTranslations = $this->option('skip-translations');
        $locales = array_map('trim', explode(',', $this->option('locales')));

        $query = Exercise::query()->whereNull('deleted_at');

        if ($ids = $this->option('ids')) {
            $query->whereIn('id', array_map('intval', explode(',', $ids)));
        }

        if (! $force && ! $skipEnrichment) {
            $query->where(function (Builder $q) {
                $q->whereNull('description')->orWhereNull('movement_pattern');
            });
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $exercises = $query->get();

        if ($exercises->isEmpty()) {
            $this->info('No exercises found matching the criteria.');

            return Command::SUCCESS;
        }

        $enrichmentCount = 0;
        $translationCount = 0;

        foreach ($exercises as $exercise) {
            if ($dryRun) {
                $this->line("  Would process: [{$exercise->id}] {$exercise->name}");

                continue;
            }

            if (! $skipEnrichment) {
                EnrichExerciseJob::dispatch($exercise, $force);
                $enrichmentCount++;
            }

            if (! $skipTranslations) {
                foreach ($locales as $locale) {
                    EnrichExerciseTranslationsJob::dispatch($exercise, $locale);
                    $translationCount++;
                }
            }
        }

        if ($dryRun) {
            $this->info("Dry run: {$exercises->count()} exercises would be processed.");

            if (! $skipEnrichment) {
                $this->info("  Enrichment jobs: {$exercises->count()}");
            }

            if (! $skipTranslations) {
                $this->info('  Translation jobs: '.$exercises->count() * count($locales));
            }

            return Command::SUCCESS;
        }

        $this->info("Dispatched {$enrichmentCount} enrichment jobs and {$translationCount} translation jobs.");
        $this->info('Run a queue worker to process: php artisan queue:work --queue=exercises');

        return Command::SUCCESS;
    }
}
