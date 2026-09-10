<?php

use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;

function renderWorkoutV2Pdf(): string
{
    app()->setLocale('de');

    $user = User::factory()->create();
    UserProfile::factory()->for($user)->buildMuscle()->create();
    $plan = Plan::factory()->for($user)->create();
    $workoutPlan = WorkoutPlan::factory()->for($plan)->create([
        'status' => 'generated',
        'workout_type' => 'strength',
        'workout_name' => 'Upper Body A',
        'description' => 'Fokus auf saubere Technik.',
    ]);
    WorkoutPlanExercise::factory()->for($workoutPlan)->create([
        'exercise_id' => null, 'type' => 'warmup', 'duration_seconds' => 120,
    ]);
    WorkoutPlanExercise::factory()->for($workoutPlan)->create([
        'exercise_id' => null, 'type' => 'strength', 'sets' => 4, 'reps' => 8, 'rpe' => '7-9',
    ]);

    return view('pdf.workout_plan_v2', [
        'user' => $user,
        'plan' => $plan,
        'workoutPlans' => $plan->workoutPlans()->with('exercises')->orderBy('day_number')->get(),
    ])->render();
}

it('renders the v2 workout template with brand fonts and sections', function () {
    expect(renderWorkoutV2Pdf())
        ->toContain('Space Grotesk')
        ->toContain('Upper Body A')
        ->toContain('Hauptteil')
        ->toContain('Warm-up');
});

it('shows the coach note and the rpe in the v2 template', function () {
    expect(renderWorkoutV2Pdf())
        ->toContain('Mona')
        ->toContain('Fokus auf saubere Technik.')
        ->toContain('7-9');
});
