<?php

use App\Enums\MuscleGroup;
use App\Models\Exercise;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Exercise::withTrashed()->whereNotNull('primary_muscles')->each(function (Exercise $exercise) {
            $normalized = collect($exercise->primary_muscles)
                ->map(fn (string $muscle) => MuscleGroup::fromRaw($muscle)?->value)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($normalized !== $exercise->primary_muscles) {
                $exercise->updateQuietly(['primary_muscles' => $normalized]);
            }
        });

        Exercise::withTrashed()->whereNotNull('secondary_muscles')->each(function (Exercise $exercise) {
            $normalized = collect($exercise->secondary_muscles)
                ->map(fn (string $muscle) => MuscleGroup::fromRaw($muscle)?->value)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($normalized !== $exercise->secondary_muscles) {
                $exercise->updateQuietly(['secondary_muscles' => $normalized]);
            }
        });

        WorkoutPlanExercise::whereNotNull('muscle_groups')->each(function (WorkoutPlanExercise $exercise) {
            $normalized = collect($exercise->muscle_groups)
                ->map(fn (string $muscle) => MuscleGroup::fromRaw($muscle)?->value)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($normalized !== $exercise->muscle_groups) {
                $exercise->updateQuietly(['muscle_groups' => $normalized]);
            }
        });

        WorkoutPlan::whereNotNull('muscle_groups')->each(function (WorkoutPlan $workoutPlan) {
            $normalized = collect($workoutPlan->muscle_groups)
                ->map(fn (string $muscle) => MuscleGroup::fromRaw($muscle)?->value)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($normalized !== $workoutPlan->muscle_groups) {
                $workoutPlan->updateQuietly(['muscle_groups' => $normalized]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible — original values are not preserved.
    }
};
