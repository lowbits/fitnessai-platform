<?php

namespace App\Console\Commands\Exercises;

use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExtractExercisesCommand extends Command
{
    protected $signature = 'exercises:extract
                            {--dry-run : Show what would be created without writing to the database}
                            {--limit= : Limit the number of exercises to process}
                            {--fresh : Clear all exercises before extracting}';

    protected $description = 'Extract unique exercises from workout_plan_exercises into the exercises master table';

    private int $created = 0;
    private int $skipped = 0;
    private int $translations = 0;
    private int $missingEnglish = 0;
    private array $duplicateSlugs = [];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        if ($dryRun) {
            $this->warn('🔍 Dry run mode — no data will be written.');
        }

        if ($limit) {
            $this->info("🧪 Limiting to {$limit} exercises.");
        }

        if (! $dryRun && $this->option('fresh')) {
            if (! $this->confirm('This will delete all existing exercises. Continue?')) {
                return Command::SUCCESS;
            }

            Schema::disableForeignKeyConstraints();
            ExerciseTranslation::truncate();
            Exercise::truncate();
            Schema::enableForeignKeyConstraints();

            $this->info('🗑️  Cleared all exercises.');
        }

        $uniqueNames = $this->getUniqueExerciseNames($limit);
        $this->info("Found {$uniqueNames->count()} unique exercises to process.");

        $bar = $this->output->createProgressBar($uniqueNames->count());
        $bar->start();

        $uniqueNames->chunk(50)->each(
            fn ($batch) => $this->processBatch($batch->toArray(), $dryRun, $bar)
        );

        $bar->finish();
        $this->newLine(2);

        $this->printSummary($dryRun, $uniqueNames->count());

        return Command::SUCCESS;
    }

    private function getUniqueExerciseNames(?int $limit)
    {
        $query = DB::table('workout_plan_exercises')
            ->whereNotNull('original_name')
            ->select(DB::raw('LOWER(TRIM(original_name)) as canonical_name'))
            ->distinct()
            ->orderBy('canonical_name');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->pluck('canonical_name');
    }

    private function processBatch(array $canonicalNames, bool $dryRun, $bar): void
    {
        $variants = $this->fetchVariants($canonicalNames);
        $grouped = $variants->groupBy(fn ($v) => Str::lower(trim($v->original_name)));

        foreach ($grouped as $key => $exerciseVariants) {
            $bar->advance();

            $result = $this->buildExerciseData($key, $exerciseVariants);

            if ($result === null) {
                $this->skipped++;
                continue;
            }

            if ($dryRun) {
                continue;
            }

            $exercise = Exercise::create($result['exercise']);
            $this->created++;

            foreach ($result['translations'] as $translation) {
                $exercise->translations()->create($translation);
                $this->translations++;
            }
        }
    }

    private function fetchVariants(array $canonicalNames)
    {
        return DB::table('workout_plan_exercises')
            ->join('workout_plans', 'workout_plan_exercises.workout_plan_id', '=', 'workout_plans.id')
            ->join('plans', 'workout_plans.plan_id', '=', 'plans.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->select([
                'workout_plan_exercises.original_name',
                'workout_plan_exercises.name',
                'workout_plan_exercises.type',
                'workout_plan_exercises.description',
                'workout_plan_exercises.instructions',
                'workout_plan_exercises.form_cues',
                'workout_plan_exercises.muscle_groups',
                'workout_plan_exercises.equipment',
                'workout_plan_exercises.difficulty',
                'workout_plan_exercises.video_url',
                'workout_plan_exercises.image',
                'users.locale',
            ])
            ->whereIn(DB::raw('LOWER(TRIM(workout_plan_exercises.original_name))'), $canonicalNames)
            ->get();
    }

    private function buildExerciseData(string $key, $variants): ?array
    {
        $english = $this->bestVariantForLocale($variants, 'en');
        $german = $this->bestVariantForLocale($variants, 'de');

        $base = $variants->first();
        $canonicalName = trim($base->original_name);

        if (! $english) {
            $this->missingEnglish++;
        }

        $slug = Str::slug($canonicalName);

        if (empty($slug)) {
            $this->newLine();
            $this->warn("  Skipping '{$canonicalName}' — cannot generate slug.");

            return null;
        }

        $slug = $this->uniqueSlug($slug);

        return [
            'exercise' => [
                'name' => $canonicalName,
                'slug' => $slug,
                'type' => $base->type,
                'difficulty' => $this->pickDifficulty($variants),
                'description' => $english?->description,
                'instructions' => $this->decodeJson($english?->instructions),
                'form_cues' => $english?->form_cues,
                'video_url' => $variants->whereNotNull('video_url')->first()?->video_url,
                'image' => $variants->whereNotNull('image')->first()?->image,
                'primary_muscles' => $this->mergeJsonField($variants, 'muscle_groups'),
                'equipment' => $this->mergeJsonField($variants, 'equipment'),
                'is_verified' => false,
            ],
            'translations' => $this->buildTranslations($variants, $canonicalName, $english, $german),
        ];
    }

    private function buildTranslations($variants, string $canonicalName, $english, $german): array
    {
        $translations = [];

        // English aliases
        $englishAliases = $this->collectAliases($variants, 'en', $canonicalName);

        if (! empty($englishAliases)) {
            $translations[] = [
                'locale' => 'en',
                'name' => $canonicalName,
                'aliases' => $englishAliases,
            ];
        }

        // German translation
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
                'aliases' => ! empty($germanAliases) ? $germanAliases : null,
                'description' => $german->description,
                'instructions' => $this->decodeJson($german->instructions),
                'form_cues' => $german->form_cues,
            ];
        }

        return $translations;
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

            return $slug . '-' . $this->duplicateSlugs[$slug];
        }

        $this->duplicateSlugs[$slug] = 0;

        return $slug;
    }

    private function pickDifficulty($variants): ?string
    {
        $priority = ['advanced' => 3, 'intermediate' => 2, 'beginner' => 1];
        $best = null;
        $bestScore = 0;

        foreach ($variants->pluck('difficulty')->filter()->unique() as $d) {
            $score = $priority[Str::lower($d)] ?? 0;

            if ($score > $bestScore) {
                $best = $d;
                $bestScore = $score;
            }
        }

        return $best;
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
            ->map(fn ($v) => trim($v))
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
                ['Unique exercises found', $total],
                ['Exercises created', $label($this->created)],
                ['Translations created', $label($this->translations)],
                ['Missing English content', $this->missingEnglish],
                ['Skipped', $this->skipped],
            ]
        );

        if ($this->missingEnglish > 0) {
            $this->warn("⚠️  {$this->missingEnglish} exercises have no English content. Run exercises:enrich to fill them.");
        }
    }
}
