<?php

namespace App\Console\Commands\Exercises;

use App\Jobs\GenerateExerciseImage;
use App\Models\Exercise;
use Illuminate\Console\Command;

class GenerateExerciseImages extends Command
{
    protected $signature = 'exercises:generate-images
                            {--exercise= : Generate image for a specific exercise (by slug)}
                            {--limit=0 : Limit the number of exercises to process}
                            {--overwrite : Overwrite existing images}
                            {--dry-run : Preview which exercises would be processed}
                            {--sync : Run synchronously instead of dispatching to queue}
                            {--gender= : Force gender (male/female), otherwise random}';

    protected $description = 'Generate AI anatomical exercise images and store locally';

    public function handle(): int
    {
        $query = Exercise::query()->orderBy('name');

        if ($slug = $this->option('exercise')) {
            $exercise = Exercise::where('slug', $slug)->first();

            if (! $exercise) {
                $this->error("Exercise not found: {$slug}");

                return Command::FAILURE;
            }

            $exercises = collect([$exercise]);
        } else {
            if (! $this->option('overwrite')) {
                $query->where(function ($q) {
                    $q->whereNull('image')->orWhere('image', '');
                });
            }

            if ($limit = (int) $this->option('limit')) {
                $query->limit($limit);
            }

            $exercises = $query->get();
        }

        if ($exercises->isEmpty()) {
            $this->info('No exercises to process. All images already exist (use --overwrite to regenerate).');

            return Command::SUCCESS;
        }

        $this->info($exercises->count().' exercise(s) to process.');

        if ($this->option('dry-run')) {
            $this->table(
                ['Slug', 'Name', 'Has Image'],
                $exercises->map(fn (Exercise $e) => [$e->slug, $e->name, $e->image ? 'Yes' : 'No'])->toArray(),
            );
            $this->info('Dry run complete. No jobs dispatched.');

            return Command::SUCCESS;
        }

        $dispatched = 0;

        foreach ($exercises as $exercise) {
            $gender = $this->option('gender') ?? (random_int(0, 1) ? 'male' : 'female');

            if ($this->option('sync')) {
                $this->line("Generating image for {$exercise->name} ({$gender})...");
                GenerateExerciseImage::dispatchSync($exercise, $gender);
            } else {
                GenerateExerciseImage::dispatch($exercise, $gender);
                $this->line("Dispatched job for: {$exercise->name} ({$gender})");
            }

            $dispatched++;
        }

        $this->info("Processed {$dispatched} exercise(s).");

        return Command::SUCCESS;
    }
}
