<?php

namespace App\Http\Controllers\Api\V2\Concerns;

trait MapsExercises
{
    /**
     * Map exercise to array format for storage
     * (used when saving exercise data, e.g., in skip workout)
     */
    protected function mapExerciseToArray($exercise): array
    {
        $canonical = $exercise->exercise;

        return [
            'id' => $exercise->id,
            'exercise_id' => $exercise->exercise_id,
            'order' => $exercise->order,
            'name' => $canonical?->localizedName() ?? '',
            'type' => $exercise->type,
            'description' => $canonical?->localizedDescription(),
            'instructions' => $canonical?->localizedInstructions(),
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $canonical?->primary_muscles ?? [],
            'equipment' => $canonical?->equipment ?? [],
            'equipment_details' => $this->mapEquipment($canonical?->equipment ?? []),
            'form_cues' => $canonical?->localizedFormCues(),
            'alternatives' => $this->mapAlternatives($exercise->alternatives),
            'alternatives_details' => $this->mapAlternativesDetails($exercise->alternatives),
            'difficulty' => $exercise->difficulty ?? $canonical?->difficulty,
        ];
    }

    /**
     * Map exercise to API response format
     * (used when returning exercise data to client)
     */
    protected function mapExerciseToResponse($exercise, $latestTracking = null): array
    {
        $canonical = $exercise->exercise;

        return [
            'id' => $exercise->id,
            'exercise_id' => $exercise->exercise_id,
            'order' => $exercise->order,
            'name' => $canonical?->localizedName() ?? '',
            'type' => $exercise->type,
            'description' => $canonical?->localizedDescription(),
            'instructions' => $canonical?->localizedInstructions(),
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'execution_style' => $exercise->execution_style,
            'rpe' => $exercise->rpe,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $canonical?->primary_muscles ?? [],
            'equipment' => $canonical?->equipment ?? [],
            'equipment_details' => $this->mapEquipment($canonical?->equipment ?? []),
            'form_cues' => $canonical?->localizedFormCues(),
            'alternatives' => $this->mapAlternatives($exercise->alternatives),
            'alternatives_details' => $this->mapAlternativesDetails($exercise->alternatives),
            'difficulty' => $exercise->difficulty ?? $canonical?->difficulty,
            'video_url' => $canonical?->video_url,
            'image' => $canonical?->image,
            'latest_tracking' => $latestTracking,
        ];
    }

    /**
     * Map equipment strings to objects with name, label and image_url
     */
    protected function mapEquipment(array $equipment): array
    {
        $baseUrl = config('services.r2.public_url');

        return collect($equipment)->map(function (string $value) use ($baseUrl) {
            $enum = \App\Enums\Equipment::tryFrom($value);

            return [
                'name' => $value,
                'label' => $enum?->label() ?? $value,
                'image_url' => "{$baseUrl}/equipments/transparent/{$value}.webp",
            ];
        })->values()->all();
    }

    /**
     * Map alternatives from [{exercise_id, name}] to clean name array
     */
    protected function mapAlternatives(?array $alternatives): array
    {
        if (empty($alternatives)) {
            return [];
        }

        return collect($alternatives)->map(function ($item) {
            if (is_string($item)) {
                return $item;
            }

            return $item['name'] ?? null;
        })->filter()->values()->all();
    }

    /**
     * Map alternatives to detailed objects with equipment and image
     */
    protected function mapAlternativesDetails(?array $alternatives): array
    {
        if (empty($alternatives)) {
            return [];
        }

        $exerciseIds = collect($alternatives)
            ->filter(fn ($item) => is_array($item) && isset($item['exercise_id']))
            ->pluck('exercise_id')
            ->all();

        if (empty($exerciseIds)) {
            return [];
        }

        $exercises = \App\Models\Exercise::with('translations')
            ->whereIn('id', $exerciseIds)
            ->get()
            ->keyBy('id');

        return collect($alternatives)
            ->filter(fn ($item) => is_array($item) && isset($item['exercise_id']))
            ->map(function ($item) use ($exercises) {
                $exercise = $exercises->get($item['exercise_id']);

                return [
                    'exercise_id' => $item['exercise_id'],
                    'name' => $exercise?->localizedName() ?? $item['name'] ?? null,
                    'equipment' => $exercise?->equipment ?? [],
                    'equipment_details' => $this->mapEquipment($exercise?->equipment ?? []),
                    'image' => $exercise?->image,
                ];
            })->filter()->values()->all();
    }
}
