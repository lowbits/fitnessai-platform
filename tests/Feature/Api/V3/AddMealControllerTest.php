<?php

use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('adds a chosen recipe as a new meal on today', function () {
    $user = User::factory()->withProfile()->onTrial()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today()]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => today(), 'day_number' => 1]);
    $recipe = Recipe::factory()->create();

    $this->actingAs($user)->postJson(route('v3.meals.add'), [
        'recipe_id' => $recipe->id,
        'type' => 'snack',
    ])->assertStatus(201);

    expect($mealPlan->meals()->where('type', 'snack')->count())->toBe(1);
});

test('forbids a user without access', function () {
    $user = User::factory()->withProfile()->create();
    $recipe = Recipe::factory()->create();

    $this->actingAs($user)->postJson(route('v3.meals.add'), [
        'recipe_id' => $recipe->id,
        'type' => 'snack',
    ])->assertForbidden();
});

test('validates recipe_id and type', function () {
    $user = User::factory()->withProfile()->onTrial()->create();

    $this->actingAs($user)->postJson(route('v3.meals.add'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['recipe_id', 'type']);
});
