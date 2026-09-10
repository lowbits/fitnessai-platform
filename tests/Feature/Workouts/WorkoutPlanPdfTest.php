<?php

use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;

function renderWorkoutPdf(string $goalState): string
{
    app()->setLocale('en');

    $user = User::factory()->create();
    UserProfile::factory()->for($user)->{$goalState}()->create();
    $plan = Plan::factory()->for($user)->create();
    $workoutPlan = WorkoutPlan::factory()->for($plan)->create([
        'status' => 'generated',
        'workout_type' => 'strength',
    ]);
    WorkoutPlanExercise::factory()->for($workoutPlan)->create([
        'exercise_id' => null,
        'type' => 'strength',
        'rpe' => '7-9',
    ]);

    return view('pdf.workout_plan', [
        'user' => $user,
        'plan' => $plan,
        'workoutPlans' => $plan->workoutPlans()->with('exercises')->orderBy('day_number')->get(),
    ])->render();
}

it('renders the goal-based progression guidance', function () {
    expect(renderWorkoutPdf('buildMuscle'))
        ->toContain('Progressive overload')
        ->toContain('increase the weight and drop back');
});

it('renders the exercise rpe so intensity is visible', function () {
    expect(renderWorkoutPdf('buildMuscle'))->toContain('RPE 7-9');
});
