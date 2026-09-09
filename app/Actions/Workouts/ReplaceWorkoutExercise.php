<?php

namespace App\Actions\Workouts;

use App\Models\Exercise;
use App\Models\WorkoutPlanExercise;

/**
 * Points a workout's exercise at a different one. Shared by the coach tool and
 * the HTTP controller so both swap an exercise the same way.
 */
class ReplaceWorkoutExercise
{
    public function execute(WorkoutPlanExercise $workoutExercise, Exercise $replacement): WorkoutPlanExercise
    {
        $workoutExercise->update(['exercise_id' => $replacement->id]);

        return $workoutExercise->refresh()->load('exercise');
    }
}
