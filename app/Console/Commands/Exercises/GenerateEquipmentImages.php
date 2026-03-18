<?php

namespace App\Console\Commands\Exercises;

use App\Enums\Equipment;
use App\Jobs\GenerateEquipmentImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateEquipmentImages extends Command
{
    protected $signature = 'equipment:generate-images
                            {--equipment= : Generate image for a specific equipment (enum value, e.g. "barbell")}
                            {--overwrite : Overwrite existing images}
                            {--dry-run : Preview which equipment would be processed}
                            {--sync : Run synchronously instead of dispatching to queue}';

    protected $description = 'Generate AI preview images for gym equipment and store locally';

    public function handle(): int
    {
        $equipmentCases = $this->getEquipmentCases();

        if ($equipmentCases === null) {
            return Command::FAILURE;
        }

        if (! $this->option('overwrite')) {
            $equipmentCases = array_filter(
                $equipmentCases,
                fn (Equipment $e) => ! Storage::disk('public')->exists("equipments/{$e->value}.webp"),
            );
        }

        if (empty($equipmentCases)) {
            $this->info('No equipment to process. All images already exist (use --overwrite to regenerate).');

            return Command::SUCCESS;
        }

        $this->info(count($equipmentCases).' equipment item(s) to process.');

        if ($this->option('dry-run')) {
            $this->table(
                ['Value', 'Label'],
                array_map(fn (Equipment $e) => [$e->value, $e->label()], $equipmentCases),
            );
            $this->info('Dry run complete. No jobs dispatched.');

            return Command::SUCCESS;
        }

        $dispatched = 0;

        foreach ($equipmentCases as $equipment) {
            if ($this->option('sync')) {
                $this->line("Generating image for {$equipment->value}...");
                GenerateEquipmentImage::dispatchSync($equipment);
            } else {
                GenerateEquipmentImage::dispatch($equipment);
                $this->line("Dispatched job for: {$equipment->value}");
            }

            $dispatched++;
        }

        $this->info("Processed {$dispatched} equipment item(s).");

        return Command::SUCCESS;
    }

    /**
     * @return Equipment[]|null
     */
    private function getEquipmentCases(): ?array
    {
        if ($value = $this->option('equipment')) {
            $equipment = Equipment::tryFrom($value);

            if (! $equipment) {
                $this->error("Invalid equipment: {$value}. Valid options: ".implode(', ', array_column(Equipment::cases(), 'value')));

                return null;
            }

            return [$equipment];
        }

        return Equipment::cases();
    }
}
