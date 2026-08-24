<?php

namespace App\Actions;

use App\Models\User;
use App\Models\WorkoutPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Moves or skips a user's workout, in the user's own words via the coach or via
 * the app. "Skip" turns today into a rest day; "move" copies the session to a
 * later day and rests today. Kept as an action so the coach tool and the HTTP
 * controllers share one implementation.
 */
class RescheduleWorkout
{
    /**
     * Turn a workout day into a rest day (the session is dropped, not moved).
     *
     * @return array{outcome: string, rest_day: array<string, mixed>}
     */
    public function skip(User $user, WorkoutPlan $workout): array
    {
        return DB::transaction(function () use ($user, $workout) {
            $date = $workout->date;
            $dayNumber = $workout->day_number;
            $planId = $workout->plan_id;

            $workout->exercises()->delete();
            $workout->delete();

            $restDay = WorkoutPlan::create([
                'plan_id' => $planId,
                'date' => $date,
                'day_number' => $dayNumber,
                'status' => 'generated',
                'workout_name' => __('workouts.active_recovery', [], $user->locale),
                'workout_type' => 'rest',
                'description' => __('workouts.rest_description', [], $user->locale),
                'estimated_duration_minutes' => 0,
            ]);

            return [
                'outcome' => 'skipped',
                'rest_day' => $this->summary($restDay),
            ];
        });
    }

    /**
     * Copy the workout to $targetDate and rest today. Returns a target_conflict
     * outcome when the target already holds a real workout and $force is false.
     *
     * @return array{outcome: string, moved_to?: array<string, mixed>, conflict?: array<string, mixed>}
     */
    public function move(User $user, WorkoutPlan $workout, Carbon $targetDate, bool $force = false): array
    {
        $plan = $workout->plan;
        $targetDate = $targetDate->copy()->startOfDay();
        $targetDayNumber = $plan->start_date->diffInDays($targetDate) + 1;

        if ($targetDate->isSameDay($workout->date)) {
            return ['outcome' => 'same_day'];
        }

        if ($targetDate->isPast() && ! $targetDate->isToday()) {
            return ['outcome' => 'in_past'];
        }

        if ($targetDayNumber < 1 || $targetDayNumber > $plan->duration_days) {
            return ['outcome' => 'outside_plan'];
        }

        $targetWorkout = WorkoutPlan::where('plan_id', $plan->id)
            ->where('day_number', $targetDayNumber)
            ->first();

        if ($targetWorkout && $targetWorkout->workout_type !== 'rest' && ! $force) {
            return [
                'outcome' => 'target_conflict',
                'conflict' => $this->summary($targetWorkout),
            ];
        }

        return DB::transaction(function () use ($user, $workout, $targetDate, $targetDayNumber, $targetWorkout) {
            $workout->loadMissing('exercises');

            if ($targetWorkout) {
                $targetWorkout->exercises()->delete();
                $targetWorkout->delete();
            }

            $moved = $workout->replicate();
            $moved->date = $targetDate;
            $moved->day_number = $targetDayNumber;
            $moved->save();

            foreach ($workout->exercises as $exercise) {
                $copy = $exercise->replicate();
                $copy->workout_plan_id = $moved->id;
                $copy->save();
            }

            $workout->exercises()->delete();
            $workout->update([
                'workout_name' => __('workouts.active_recovery', [], $user->locale),
                'workout_type' => 'rest',
                'estimated_duration_minutes' => 0,
                'estimated_calories_burned' => 0,
                'difficulty' => 'easy',
                'description' => __('workouts.rest_description', [], $user->locale),
                'muscle_groups' => [],
                'status' => 'generated',
            ]);

            return [
                'outcome' => 'moved',
                'moved_to' => $this->summary($moved),
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(WorkoutPlan $workout): array
    {
        return [
            'workout_id' => $workout->id,
            'name' => $workout->workout_name,
            'type' => $workout->workout_type,
            'date' => $workout->date?->format('Y-m-d'),
        ];
    }
}
