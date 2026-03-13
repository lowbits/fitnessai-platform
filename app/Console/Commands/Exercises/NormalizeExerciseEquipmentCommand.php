<?php

namespace App\Console\Commands\Exercises;

use App\Enums\Equipment;
use App\Models\Exercise;
use Illuminate\Console\Command;

class NormalizeExerciseEquipmentCommand extends Command
{
    protected $signature = 'exercises:normalize-equipment
        {--ids= : Comma-separated exercise IDs to normalize}
        {--dry-run : Show changes without saving}';

    protected $description = 'Normalize exercise equipment values to canonical Equipment enum values';

    public function handle(): int
    {
        $query = Exercise::query()
            ->whereNotNull('equipment')
            ->whereNull('deleted_at');

        if ($ids = $this->option('ids')) {
            $query->whereIn('id', array_map('intval', explode(',', $ids)));
        }

        $dryRun = $this->option('dry-run');
        $updated = 0;
        $skipped = 0;
        $unmapped = [];

        $query->chunkById(200, function ($exercises) use ($dryRun, &$updated, &$skipped, &$unmapped) {
            foreach ($exercises as $exercise) {
                $original = $exercise->equipment ?? [];
                $normalized = $this->normalizeEquipment($original, $exercise, $unmapped);

                if ($original === $normalized) {
                    $skipped++;

                    continue;
                }

                if ($dryRun) {
                    $this->line("  [{$exercise->id}] {$exercise->name}");
                    $this->line('    Before: '.json_encode($original));
                    $this->line('    After:  '.json_encode($normalized));
                } else {
                    $exercise->update(['equipment' => $normalized]);
                }

                $updated++;
            }
        });

        if ($dryRun) {
            $this->info("Dry run: {$updated} exercises would be updated, {$skipped} already normalized.");
        } else {
            $this->info("Updated {$updated} exercises, {$skipped} already normalized.");
        }

        if (! empty($unmapped)) {
            $this->warn('Unmapped equipment values (dropped during normalization):');

            foreach (array_unique($unmapped) as $value) {
                $this->line("  - {$value}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param  array<string>  $equipment
     * @param  array<string>  $unmapped
     * @return array<string>
     */
    private function normalizeEquipment(array $equipment, Exercise $exercise, array &$unmapped): array
    {
        $validValues = array_map(fn (Equipment $e) => $e->value, Equipment::cases());

        return collect($equipment)
            ->map(function (string $item) use ($validValues, $exercise, &$unmapped) {
                if (in_array($item, $validValues)) {
                    return $item;
                }

                $mapped = Equipment::fromRaw($item)?->value;

                if ($mapped === null) {
                    $unmapped[] = "[{$exercise->id}] {$exercise->name}: \"{$item}\"";
                }

                return $mapped;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
