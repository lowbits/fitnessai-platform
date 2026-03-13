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
            'name' => $canonical?->localizedName() ?? $exercise->name,
            'type' => $exercise->type,
            'description' => $exercise->description ?? $canonical?->localizedDescription(),
            'instructions' => $exercise->instructions ?? $canonical?->localizedInstructions(),
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $exercise->muscle_groups ?? $canonical?->primary_muscles ?? [],
            'equipment' => $exercise->equipment ?? $canonical?->equipment ?? [],
            'form_cues' => $exercise->form_cues ?? $canonical?->localizedFormCues(),
            'alternatives' => $this->mapAlternatives($exercise->alternatives),
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
            'name' => $canonical?->localizedName() ?? $exercise->name,
            'type' => $exercise->type,
            'description' => $exercise->description ?? $canonical?->localizedDescription(),
            'instructions' => $exercise->instructions ?? $canonical?->localizedInstructions(),
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'execution_style' => $exercise->execution_style,
            'rpe' => $exercise->rpe,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $exercise->muscle_groups ?? $canonical?->primary_muscles ?? [],
            'equipment' => $exercise->equipment ?? $canonical?->equipment ?? [],
            'form_cues' => $exercise->form_cues ?? $canonical?->localizedFormCues(),
            'alternatives' => $this->mapAlternatives($exercise->alternatives),
            'difficulty' => $exercise->difficulty ?? $canonical?->difficulty,
            'video_url' => $canonical?->video_url ?? $exercise->video_url,
            'image' => $canonical?->image ?? $exercise->image,
            'latest_tracking' => $latestTracking,
        ];
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
}
