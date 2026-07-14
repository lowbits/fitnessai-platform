<?php

use App\Ai\Tools\CreateRecipeTool;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;

uses(RefreshDatabase::class);

function replaceableMeal(User $user): Meal
{
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'status' => 'generated']);

    return Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
        'calories' => 800,
        'protein_g' => 45,
        'completed_at' => null,
    ]);
}

it('rejects a blank request without touching the meal', function () {
    $user = User::factory()->withProfile()->create();

    $result = json_decode((new CreateRecipeTool($user))->handle(new Request([
        'meal_id' => 999,
        'request' => '   ',
    ])), true);

    expect($result['error'])->toBe('missing_request');
});

it('refuses a meal the user does not own', function () {
    $user = User::factory()->withProfile()->create();
    $otherMeal = replaceableMeal(User::factory()->withProfile()->create());

    $result = json_decode((new CreateRecipeTool($user))->handle(new Request([
        'meal_id' => $otherMeal->id,
        'request' => 'Chili con Carne',
    ])), true);

    expect($result['error'])->toBe('meal_not_found');
});

it('refuses to replace an already-eaten meal', function () {
    $user = User::factory()->withProfile()->create();
    $meal = replaceableMeal($user);
    $meal->update(['completed_at' => now()]);

    $result = json_decode((new CreateRecipeTool($user))->handle(new Request([
        'meal_id' => $meal->id,
        'request' => 'Chili con Carne',
    ])), true);

    expect($result['error'])->toBe('meal_already_eaten');
});
