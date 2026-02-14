<?php

namespace App\Console\Commands;

use App\Ai\Agents\WorkoutProgrammerAgent;
use App\Ai\Prompts\CreateWorkoutPrompt;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Console\Command;

use function Laravel\Prompts\spin;

class CreateWorkoutCommand extends Command
{
    protected $signature = 'create:workout';

    protected $description = 'Create a workout plan for the latest user using the WorkoutCoachAgent.';

    public function handle(): void
    {
        $user = User::latest()->first();
        $dayNumber = 1;

        $plan = $user->plans()->firstOrFail();

        $workoutPlan = WorkoutPlan::firstOrCreate(
            [
                'plan_id' => $plan->id,
                'day_number' => $dayNumber,
            ],
            [
                'date' => now()->format('Y-m-d'),
                'status' => 'pending',
                'workout_name' => 'Workout Day',
                'workout_type' => 'strength',
            ]
        );

        $prompt = new CreateWorkoutPrompt(
            profile: $user->profile,
            locale: $user->preferredLocale(),
            dayNumber: $dayNumber,
            workoutsPerWeek: 2
        );

        (new WorkoutProgrammerAgent($workoutPlan))
            ->prompt((string) $prompt);

        $start = microtime(true);

        spin(
            callback: fn () => (new WorkoutProgrammerAgent($workoutPlan))
                ->prompt((string) $prompt),
            message: 'Generating workout plan...',
        );

        $seconds = round(microtime(true) - $start, 1);

        $this->info("✅ Workout plan generated. ({$seconds}s)");
    }
}
