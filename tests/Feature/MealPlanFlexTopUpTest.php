<?php

use App\Jobs\GenerateMealPlanBatch;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function topUp(User $user, Plan $plan, MealPlan $mealPlan): void
{
    $job = new GenerateMealPlanBatch($user, $plan, 1, 1);
    $method = new ReflectionMethod($job, 'topUpWithFlexShake');
    $method->setAccessible(true);
    $method->invoke($job, $mealPlan, 1);
}

function seedMealPlan(User $user, int $totalKcal): array
{
    $plan = Plan::factory()->create(['user_id' => $user->id, 'start_date' => now()]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'generated']);
    Meal::factory()->create(['meal_plan_id' => $mealPlan->id, 'type' => 'breakfast', 'calories' => $totalKcal]);

    return [$plan, $mealPlan];
}

function highCalUser(bool $autoFill = true): User
{
    return User::factory()->withProfile([
        'selected_meals' => ['breakfast', 'lunch', 'dinner'],
        'auto_fill_calories' => $autoFill,
    ])->create();
}

it('adds a flex shake for the gap when the day lands more than 5% under target', function () {
    $user = highCalUser();
    $goal = (int) $user->profile->getMetabolismData()['daily_calories'];
    // Land ~20% under the target.
    [$plan, $mealPlan] = seedMealPlan($user, (int) round($goal * 0.8));

    topUp($user, $plan, $mealPlan);

    $flex = $mealPlan->meals()->where('type', 'flex')->first();
    expect($flex)->not->toBeNull()
        ->and($flex->calories)->toBe($goal - (int) round($goal * 0.8));
});

it('leaves the day alone when it is within the 5% tolerance', function () {
    $user = highCalUser();
    $goal = (int) $user->profile->getMetabolismData()['daily_calories'];
    [$plan, $mealPlan] = seedMealPlan($user, (int) round($goal * 0.97));

    topUp($user, $plan, $mealPlan);

    expect($mealPlan->meals()->where('type', 'flex')->exists())->toBeFalse();
});

it('does not add a flex when auto-fill is off', function () {
    $user = highCalUser(autoFill: false);
    $goal = (int) $user->profile->getMetabolismData()['daily_calories'];
    [$plan, $mealPlan] = seedMealPlan($user, (int) round($goal * 0.8));

    topUp($user, $plan, $mealPlan);

    expect($mealPlan->meals()->where('type', 'flex')->exists())->toBeFalse();
});

it('never adds a second flex when one already exists', function () {
    $user = highCalUser();
    $goal = (int) $user->profile->getMetabolismData()['daily_calories'];
    [$plan, $mealPlan] = seedMealPlan($user, (int) round($goal * 0.6));
    Meal::factory()->create(['meal_plan_id' => $mealPlan->id, 'type' => 'flex', 'calories' => 300]);

    topUp($user, $plan, $mealPlan);

    expect($mealPlan->meals()->where('type', 'flex')->count())->toBe(1);
});
