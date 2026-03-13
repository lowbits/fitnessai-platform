<?php

namespace App\Jobs;

use App\Ai\Agents\ExerciseEnrichmentAgent;
use App\Enums\Equipment;
use App\Enums\MuscleGroup;
use App\Models\Exercise;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EnrichExerciseJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * @return array<int>
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public function __construct(
        public Exercise $exercise,
        public bool $force = false,
    ) {
        $this->onQueue('exercises');
    }

    public function handle(): void
    {
        if ($this->exercise->deleted_at !== null) {
            Log::info('[EnrichExercise] Skipping soft-deleted exercise', [
                'exercise_id' => $this->exercise->id,
            ]);

            return;
        }

        $fieldsToEnrich = $this->determineFieldsToEnrich();

        if (empty($fieldsToEnrich)) {
            Log::info('[EnrichExercise] No fields to enrich', [
                'exercise_id' => $this->exercise->id,
            ]);

            return;
        }

        Log::info('[EnrichExercise] Starting enrichment', [
            'exercise_id' => $this->exercise->id,
            'exercise_name' => $this->exercise->name,
            'fields' => $fieldsToEnrich,
        ]);

        $agent = new ExerciseEnrichmentAgent($this->exercise, $fieldsToEnrich);
        $response = $agent->prompt($agent->buildPrompt());

        $data = $this->extractData($response, $fieldsToEnrich);

        if (empty($data)) {
            Log::warning('[EnrichExercise] No valid data returned from AI', [
                'exercise_id' => $this->exercise->id,
            ]);

            return;
        }

        $this->exercise->update($data);

        Log::info('[EnrichExercise] Completed', [
            'exercise_id' => $this->exercise->id,
            'fields_updated' => array_keys($data),
        ]);
    }

    /**
     * @return array<string>
     */
    private function determineFieldsToEnrich(): array
    {
        $allFields = [
            'movement_pattern', 'mechanic', 'force_type', 'laterality',
            'body_region', 'primary_muscles', 'secondary_muscles', 'equipment',
            'training_places', 'difficulty', 'description', 'instructions', 'form_cues',
        ];

        if ($this->force) {
            return $allFields;
        }

        return collect($allFields)
            ->filter(function (string $field) {
                $value = $this->exercise->{$field};

                return $value === null || (is_array($value) && empty($value));
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string>  $fieldsToEnrich
     * @return array<string, mixed>
     */
    private function extractData(mixed $response, array $fieldsToEnrich): array
    {
        $muscleFields = ['primary_muscles', 'secondary_muscles'];
        $data = [];

        foreach ($fieldsToEnrich as $field) {
            if (! isset($response[$field])) {
                continue;
            }

            $value = $response[$field];

            if (in_array($field, $muscleFields) && is_array($value)) {
                $value = $this->normalizeMuscles($value);
            }

            if ($field === 'equipment' && is_array($value)) {
                $value = $this->normalizeEquipment($value);
            }

            $data[$field] = $value;
        }

        return $data;
    }

    /**
     * @param  array<string>  $muscles
     * @return array<string>
     */
    private function normalizeMuscles(array $muscles): array
    {
        $validValues = array_map(fn (MuscleGroup $m) => $m->value, MuscleGroup::cases());

        return collect($muscles)
            ->map(function (string $muscle) use ($validValues) {
                if (in_array($muscle, $validValues)) {
                    return $muscle;
                }

                return MuscleGroup::fromRaw($muscle)?->value;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<string>  $equipment
     * @return array<string>
     */
    private function normalizeEquipment(array $equipment): array
    {
        $validValues = array_map(fn (Equipment $e) => $e->value, Equipment::cases());

        return collect($equipment)
            ->map(function (string $item) use ($validValues) {
                if (in_array($item, $validValues)) {
                    return $item;
                }

                return Equipment::fromRaw($item)?->value;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
