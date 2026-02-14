<?php

namespace App\Jobs;

use App\Ai\Agents\WorkoutProgrammerAgent;
use App\Ai\Prompts\CreateWorkoutPrompt;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateUserWorkoutPlan implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public Plan $plan,
    ) {
        $this->onQueue('workouts');
    }

    public function handle(): void
    {
        $profile = $this->user->profile;

        if (! $profile) {
            Log::error('User profile not found', ['user_id' => $this->user->id]);

            return;
        }

        [$startDay, $endDay] = $this->calculateDayRange();

        if (! $startDay) {
            Log::info('Workout plan already complete', [
                'user_id' => $this->user->id,
                'plan_id' => $this->plan->id,
            ]);

            return;
        }

        Log::info('Starting workout plan generation', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'start_day' => $startDay,
            'end_day' => $endDay,
        ]);

        $workoutsPerWeek = $this->plan->workouts_per_week ?? 3;
        $generatedWorkoutsSummary = [];

        for ($day = $startDay; $day <= $endDay; $day++) {
            $date = $this->plan->start_date->copy()->addDays($day - 1);

            if ($date->gt($this->plan->end_date) || $day > $this->plan->duration_days) {
                break;
            }

            $isRestDay = $this->isRestDay($day, $workoutsPerWeek);

            $workoutPlan = WorkoutPlan::firstOrCreate(
                ['plan_id' => $this->plan->id, 'day_number' => $day],
                ['date' => $date, 'status' => 'pending', 'workout_name' => $isRestDay ? 'Rest Day' : 'Workout Day', 'workout_type' => $isRestDay ? 'rest' : 'strength'],
            );

            // Skip already generated
            if ($workoutPlan->status === 'generated') {
                if (! $isRestDay) {
                    $exerciseNames = $workoutPlan->exercises()->with('exercise')->get()->map(fn ($e) => $e->exercise?->name ?? $e->name)->take(3)->implode(', ');
                    $generatedWorkoutsSummary[] = "Day $day: $workoutPlan->workout_name ($exerciseNames...)";
                }

                continue;
            }

            // Handle rest days without AI
            if ($isRestDay) {
                $workoutPlan->update([
                    'status' => 'generated',
                    'workout_name' => __('workouts.active_recovery', [], $this->user->locale),
                    'workout_type' => 'rest',
                    'description' => __('workouts.rest_description', [], $this->user->locale),
                    'estimated_duration_minutes' => 0,
                ]);

                continue;
            }

            // Generate workout via AI agent
            try {
                $prompt = new CreateWorkoutPrompt(
                    profile: $profile,
                    locale: $this->user->locale,
                    dayNumber: $day,
                    workoutsPerWeek: $workoutsPerWeek,
                    recentWorkouts: array_slice($generatedWorkoutsSummary, -5),
                );

                (new WorkoutProgrammerAgent($workoutPlan))
                    ->prompt((string) $prompt);

                // Refresh to get updated data from SaveWorkoutPlanTool tool
                $workoutPlan->refresh();

                if ($workoutPlan->status === 'generated') {
                    $exerciseNames = $workoutPlan->exercises()->with('exercise')->get()->map(fn ($e) => $e->exercise?->name ?? $e->name)->take(3)->implode(', ');
                    $generatedWorkoutsSummary[] = "Day $day: $workoutPlan->workout_name ($exerciseNames...)";

                    Log::info("Generated workout for day $day", [
                        'workout_plan_id' => $workoutPlan->id,
                        'workout_name' => $workoutPlan->workout_name,
                    ]);
                } else {
                    Log::warning("Agent completed but workout not generated for day $day", [
                        'workout_plan_id' => $workoutPlan->id,
                        'status' => $workoutPlan->status,
                    ]);

                    $workoutPlan->update(['status' => 'failed']);
                }
            } catch (Exception $e) {
                Log::error("Failed to generate workout for day $day", [
                    'error' => $e->getMessage(),
                    'workout_plan_id' => $workoutPlan->id,
                ]);

                $workoutPlan->update(['status' => 'failed']);

                continue;
            }
        }

        Log::info('Workout plan generation completed', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'generated' => WorkoutPlan::where('plan_id', $this->plan->id)->where('status', 'generated')->count(),
            'failed' => WorkoutPlan::where('plan_id', $this->plan->id)->where('status', 'failed')->count(),
        ]);
    }

    /**
     * Calculate which days to generate.
     *
     * @return array{int|null, int|null} [startDay, endDay] or [null, null] if complete
     */
    private function calculateDayRange(): array
    {
        $firstFailedDay = WorkoutPlan::where('plan_id', $this->plan->id)
            ->where('status', 'failed')
            ->min('day_number');

        $lastGeneratedDay = WorkoutPlan::where('plan_id', $this->plan->id)
            ->where('status', 'generated')
            ->max('day_number') ?? 0;

        // Already complete
        if ($lastGeneratedDay >= $this->plan->duration_days && ! $firstFailedDay) {
            return [null, null];
        }

        $startDay = $firstFailedDay
            ? min($firstFailedDay, $lastGeneratedDay + 1)
            : $lastGeneratedDay + 1;

        $endDay = min($startDay + 6, $this->plan->duration_days); // 7 days at a time

        return [$startDay, $endDay];
    }

    private function isRestDay(int $day, int $workoutsPerWeek): bool
    {
        $profile = $this->user->profile;

        if ($profile && ! empty($profile->training_days)) {
            $dayMap = [
                'monday' => 0, 'tuesday' => 1, 'wednesday' => 2,
                'thursday' => 3, 'friday' => 4, 'saturday' => 5, 'sunday' => 6,
            ];

            $workoutDays = array_map(fn ($d) => $dayMap[strtolower($d)], $profile->training_days);

            return ! in_array(($day - 1) % 7, $workoutDays);
        }

        $workoutDays = match ($workoutsPerWeek) {
            1 => [0],
            2 => [0, 3],
            4 => [0, 2, 4, 6],
            5 => [0, 1, 2, 4, 5],
            6 => [0, 1, 2, 3, 4, 5],
            7 => [0, 1, 2, 3, 4, 5, 6],
            default => [0, 2, 4],
        };

        return ! in_array(($day - 1) % 7, $workoutDays);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('GenerateUserWorkoutPlan final failure', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'error' => $exception->getMessage(),
        ]);

        WorkoutPlan::where('plan_id', $this->plan->id)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
    }
}
