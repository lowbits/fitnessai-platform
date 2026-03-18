<?php

namespace App\Actions\Workouts;

use App\Ai\DataTransferObjects\WorkoutPlanResult;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;

class PopulateWorkoutPlanAction
{
    public function execute(WorkoutPlan $workoutPlan, WorkoutPlanResult $workoutPlanResult): void
    {
        $workoutPlan->exercises()->delete();

        $workoutPlan->update([
            'workout_name' => $workoutPlanResult->workoutName,
            'workout_type' => $workoutPlanResult->workoutType,
            'description' => $workoutPlanResult->description,
            'estimated_duration_minutes' => $workoutPlanResult->estimatedDurationMinutes,
            'difficulty' => $workoutPlanResult->difficulty,
            'muscle_groups' => $workoutPlanResult->muscleGroups,
            'status' => 'generated',
        ]);

        foreach ($workoutPlanResult->exercises as $index => $exercise) {
            WorkoutPlanExercise::create([
                'workout_plan_id' => $workoutPlan->id,
                'exercise_id' => $exercise['exercise_id'],
                'order' => $exercise['order'] ?? $index + 1,
                'type' => $exercise['type'],
                'sets' => $exercise['sets'] ?? null,
                'reps' => $exercise['reps'] ?? null,
                'duration_seconds' => $exercise['duration_seconds'] ?? null,
                'rest_seconds' => $exercise['rest_seconds'] ?? null,
                'tempo' => $exercise['tempo'] ?? null,
                'execution_style' => $exercise['execution_style'] ?? null,
                'weight_recommendation' => $exercise['weight_recommendation'] ?? null,
                'rpe' => $exercise['rpe'] ?? null,
                'alternatives' => $exercise['alternatives'] ?? [],
            ]);
        }

    }
}
