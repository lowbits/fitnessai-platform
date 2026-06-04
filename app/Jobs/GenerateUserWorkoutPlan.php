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
use Laravel\Ai\Enums\Lab;
use Throwable;

class GenerateUserWorkoutPlan implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public Plan $plan,
        public ?int $maxDays = null,
    ) {
        $this->onQueue('workouts');
    }

    public function handle(): void
    {
        $profile = $this->user->profile;

        if (! $profile) {
            Log::error('[WorkoutGen] No profile found, aborting', ['user_id' => $this->user->id]);

            return;
        }

        [$startDay, $endDay] = $this->calculateDayRange();

        if (! $startDay) {
            Log::info('[WorkoutGen] All days already generated', [
                'user_id' => $this->user->id,
                'plan_id' => $this->plan->id,
            ]);

            return;
        }

        Log::info('[WorkoutGen] Starting generation', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'day_range' => "$startDay-$endDay",
            'workouts_per_week' => $this->plan->workouts_per_week ?? 3,
            'locale' => $this->user->locale,
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
                Log::info("[WorkoutGen] Calling AI agent for day $day", [
                    'workout_plan_id' => $workoutPlan->id,
                ]);

                $prompt = new CreateWorkoutPrompt(
                    profile: $profile,
                    locale: $this->user->locale,
                    dayNumber: $day,
                    workoutsPerWeek: $workoutsPerWeek,
                    recentWorkouts: array_slice($generatedWorkoutsSummary, -5),
                );

                (new WorkoutProgrammerAgent($workoutPlan))
                    ->prompt((string) $prompt, provider: [Lab::OpenAI, Lab::Mistral], model: config('ai.models.agent'));

                // Refresh to get updated data from SaveWorkoutPlanTool tool
                $workoutPlan->refresh();

                if ($workoutPlan->status === 'generated') {
                    $exerciseNames = $workoutPlan->exercises()->with('exercise')->get()->map(fn ($e) => $e->exercise?->name ?? $e->name)->take(3)->implode(', ');
                    $generatedWorkoutsSummary[] = "Day $day: $workoutPlan->workout_name ($exerciseNames...)";

                    Log::info("[WorkoutGen] Day $day OK", [
                        'workout_plan_id' => $workoutPlan->id,
                        'workout_name' => $workoutPlan->workout_name,
                        'workout_type' => $workoutPlan->workout_type,
                        'exercises' => $workoutPlan->exercises()->count(),
                    ]);
                } else {
                    Log::warning("[WorkoutGen] Agent finished but status still '{$workoutPlan->status}' for day $day — likely SaveWorkoutPlanTool failed silently", [
                        'workout_plan_id' => $workoutPlan->id,
                        'status' => $workoutPlan->status,
                    ]);

                    $workoutPlan->update(['status' => 'failed']);
                }
            } catch (Exception $e) {
                Log::error("[WorkoutGen] Exception on day $day: {$e->getMessage()}", [
                    'workout_plan_id' => $workoutPlan->id,
                    'exception' => $e::class,
                ]);

                $workoutPlan->update(['status' => 'failed']);

                continue;
            }
        }

        $generated = WorkoutPlan::where('plan_id', $this->plan->id)->where('status', 'generated')->count();
        $failed = WorkoutPlan::where('plan_id', $this->plan->id)->where('status', 'failed')->count();
        $pending = WorkoutPlan::where('plan_id', $this->plan->id)->where('status', 'pending')->count();

        Log::info('[WorkoutGen] Batch done', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'generated' => $generated,
            'failed' => $failed,
            'pending' => $pending,
        ]);
    }

    /**
     * Calculate which days to generate.
     *
     * @return array{int|null, int|null} [startDay, endDay] or [null, null] if complete
     */
    private function calculateDayRange(): array
    {
        // Calculate today's day number within the plan
        $todayDayNumber = max(1, (int) $this->plan->start_date->diffInDays(now()->startOfDay()) + 1);
        $todayDayNumber = min($todayDayNumber, $this->plan->duration_days);

        $generatedDays = WorkoutPlan::where('plan_id', $this->plan->id)
            ->where('status', 'generated')
            ->where('day_number', '>=', $todayDayNumber)
            ->pluck('day_number')
            ->toArray();

        // Find the first day from today onwards that is not yet generated
        $startDay = null;
        for ($day = $todayDayNumber; $day <= $this->plan->duration_days; $day++) {
            if (! in_array($day, $generatedDays)) {
                $startDay = $day;
                break;
            }
        }

        // All days from today onwards are generated
        if (! $startDay) {
            return [null, null];
        }

        $daysToGenerate = $this->maxDays ?? 7;
        $endDay = min($startDay + $daysToGenerate - 1, $this->plan->duration_days);

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
        Log::error('[WorkoutGen] Job failed permanently', [
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'error' => $exception->getMessage(),
            'exception' => $exception::class,
        ]);

        WorkoutPlan::where('plan_id', $this->plan->id)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
    }
}
