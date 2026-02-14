<?php

namespace App\Ai\DataTransferObjects;

use Laravel\Ai\Tools\Request;

class WorkoutPlanResult
{
    public function __construct(
        public readonly string $workoutName,
        public readonly string $workoutType,
        public readonly ?string $description,
        public readonly ?int $estimatedDurationMinutes,
        public readonly ?string $difficulty,
        public readonly array $muscleGroups,
        public readonly array $exercises,
    ) {}

    public static function fromRequest(Request $data): static
    {
        return new static(
            workoutName: $data['workout_name'],
            workoutType: $data['workout_type'],
            description: $data['description'] ?? null,
            estimatedDurationMinutes: $data['estimated_duration_minutes'] ?? null,
            difficulty: $data['difficulty'] ?? null,
            muscleGroups: $data['muscle_groups'] ?? [],
            exercises: $data['exercises'] ?? [],
        );
    }
}
