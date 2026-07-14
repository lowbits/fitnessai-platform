<?php

use App\Models\CalorieTracking;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTracking;
use App\Services\DayCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function dcService(): DayCompletionService
{
    return app(DayCompletionService::class);
}

/** Active plan whose day 1 is today, so today's day_number is 1. */
function dcUserWithPlan(int $goal = 2000): array
{
    $user = User::factory()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'daily_calories' => $goal,
        'start_date' => today()->toDateString(),
    ]);

    return [$user, $plan];
}

function dcTrackKcal(User $user, int $kcal): void
{
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'tracked_date' => today()->toDateString(),
        'calories' => $kcal,
    ]);
}

it('applies the rule purely, without touching the database', function () {
    $s = dcService();

    expect($s->evaluate(2000, 2000, false, false, false)->isPerfect)->toBeTrue()   // rest day, goal met
        ->and($s->evaluate(2000, 2000, false, true, false)->isPerfect)->toBeFalse() // training day, no workout
        ->and($s->evaluate(2000, 2000, false, true, true)->isPerfect)->toBeTrue()   // training day + workout
        ->and($s->evaluate(1950, 2000, false, false, false)->nutritionMet)->toBeTrue()  // within 5% tolerance
        ->and($s->evaluate(1600, 2000, false, false, false)->nutritionMet)->toBeFalse() // outside tolerance
        ->and($s->evaluate(0, 2000, true, false, false)->nutritionMet)->toBeTrue();     // recommended meals met
});

it('completes a rest day on calories alone', function () {
    [$user] = dcUserWithPlan();
    dcTrackKcal($user, 2000);

    $c = dcService()->for($user, today()->toDateString());

    expect($c->nutritionMet)->toBeTrue()
        ->and($c->workoutDone)->toBeTrue()
        ->and($c->isPerfect)->toBeTrue();
});

it('does not complete a training day without the workout', function () {
    [$user, $plan] = dcUserWithPlan();
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1]);
    dcTrackKcal($user, 2000);

    $c = dcService()->for($user, today()->toDateString());

    expect($c->nutritionMet)->toBeTrue()
        ->and($c->workoutDone)->toBeFalse()
        ->and($c->isPerfect)->toBeFalse();
});

it('completes a training day once the workout is done', function () {
    [$user, $plan] = dcUserWithPlan();
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1]);
    dcTrackKcal($user, 2000);
    WorkoutTracking::factory()->create(['user_id' => $user->id, 'completed_at' => today()->toDateString()]);

    expect(dcService()->for($user, today()->toDateString())->isPerfect)->toBeTrue();
});

it('counts every recommended meal tracked as nutrition met even under goal', function () {
    [$user, $plan] = dcUserWithPlan();
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => today()->toDateString()]);
    $meals = Meal::factory()->count(3)->create(['meal_plan_id' => $mealPlan->id, 'calories' => 400]);
    $meals->each(fn (Meal $m) => CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $m->id,
        'tracked_date' => today()->toDateString(),
        'calories' => $m->calories,
    ]));

    // 1200 kcal total is well under the 1900 tolerance threshold, but the plan was eaten.
    expect(dcService()->for($user, today()->toDateString())->nutritionMet)->toBeTrue();
});

it('is not complete without an active plan', function () {
    $user = User::factory()->create();

    expect(dcService()->for($user, today()->toDateString())->isPerfect)->toBeFalse();
});
