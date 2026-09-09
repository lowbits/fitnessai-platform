<?php

use App\Actions\Workouts\ReplaceWorkoutExercise;
use App\Ai\Tools\SwapExerciseTool;
use App\Enums\TrainingPlace;
use App\Models\Exercise;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use Laravel\Ai\Tools\Request;

function swapExercise(User $user, array $args): array
{
    return json_decode((new SwapExerciseTool($user, new ReplaceWorkoutExercise))->handle(new Request($args)), true);
}

/**
 * @return array{0: User, 1: WorkoutPlanExercise, 2: Exercise}
 */
function benchWorkout(): array
{
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id, 'training_place' => TrainingPlace::GYM]);
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today(), 'duration_days' => 30]);
    $workout = WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => today(), 'day_number' => 1, 'status' => 'generated', 'workout_type' => 'strength']);

    $bench = Exercise::factory()->create(['name' => 'Bench Press', 'primary_muscles' => ['chest'], 'training_places' => ['gym']]);
    $current = WorkoutPlanExercise::factory()->for($workout, 'workoutPlan')->create(['exercise_id' => $bench->id, 'order' => 1, 'alternatives' => null]);

    $pushup = Exercise::factory()->create(['name' => 'Push Up', 'primary_muscles' => ['chest'], 'training_places' => ['gym']]);

    return [$user, $current, $pushup];
}

it('proposes same-muscle alternatives for an exercise', function () {
    [$user] = benchWorkout();

    $result = swapExercise($user, ['exercise' => 'Bench']);

    expect($result['exercise'])->toBe('Bench Press')
        ->and(collect($result['alternatives'])->pluck('name'))->toContain('Push Up');
});

it('swaps the exercise once the user picks a replacement', function () {
    [$user, $current, $pushup] = benchWorkout();

    $result = swapExercise($user, ['exercise' => 'Bench', 'replacement' => 'Push Up']);

    expect($result['swapped'])->toBeTrue()
        ->and($result['to'])->toBe('Push Up')
        ->and($current->fresh()->exercise_id)->toBe($pushup->id);
});

it('errors when the exercise is not in the workout', function () {
    [$user] = benchWorkout();

    $result = swapExercise($user, ['exercise' => 'Deadlift']);

    expect($result['error'])->toBe('exercise_not_found')
        ->and($result['exercises'])->toContain('Bench Press');
});

it('has nothing to swap on a rest day', function () {
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today()]);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => today(), 'workout_type' => 'rest', 'status' => 'generated']);

    expect(swapExercise($user, ['exercise' => 'anything'])['error'])->toBe('no_workout');
});
