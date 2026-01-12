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
        return [
            'id' => $exercise->id,
            'order' => $exercise->order,
            'name' => $exercise->name,
            'original_name' => $exercise->original_name,
            'type' => $exercise->type,
            'description' => $exercise->description,
            'instructions' => $exercise->instructions,
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $exercise->muscle_groups,
            'equipment' => $exercise->equipment,
            'form_cues' => $exercise->form_cues,
            'alternatives' => $exercise->alternatives,
            'difficulty' => $exercise->difficulty,
        ];
    }

    /**
     * Map exercise to API response format
     * (used when returning exercise data to client)
     */
    protected function mapExerciseToResponse($exercise, $latestTracking = null): array
    {
        return [
            'id' => $exercise->id,
            'order' => $exercise->order,
            'name' => $exercise->name,
            'original_name' => $exercise->original_name,
            'type' => $exercise->type,
            'description' => $exercise->description,
            'instructions' => $exercise->instructions,
            'sets' => $exercise->sets,
            'reps' => $exercise->reps,
            'duration_seconds' => $exercise->duration_seconds,
            'rest_seconds' => $exercise->rest_seconds,
            'tempo' => $exercise->tempo,
            'weight_recommendation' => $exercise->weight_recommendation,
            'muscle_groups' => $exercise->muscle_groups ?? [],
            'equipment' => $exercise->equipment ?? [],
            'form_cues' => $exercise->form_cues,
            'alternatives' => $exercise->alternatives ?? [],
            'difficulty' => $exercise->difficulty,
            'video_url' => $exercise->video_url,
            'image' => $exercise->image,
            'latest_tracking' => $latestTracking,
        ];
    }
}

