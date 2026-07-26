<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\Recipe;
use App\Models\RecipeFavorite;
use App\Models\User;
use App\Services\Recipe\RecipeAffinity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAffinityUser(): User
{
    return User::factory()->withProfile()->create();
}

function makeRecipe(): Recipe
{
    return Recipe::factory()->create();
}

function serveRecipeToUser(User $user, Recipe $recipe, array $attributes = []): Meal
{
    $plan = Plan::factory()->create(['user_id' => $user->id, 'duration_days' => 7]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'generated']);

    return Meal::factory()->create(array_merge([
        'meal_plan_id' => $mealPlan->id,
        'recipe_id' => $recipe->id,
        'type' => 'lunch',
    ], $attributes));
}

test('favorited recipe scores higher than completed-only', function () {
    $user = makeAffinityUser();
    $favorited = makeRecipe();
    $completed = makeRecipe();

    RecipeFavorite::create(['user_id' => $user->id, 'recipe_id' => $favorited->id]);
    serveRecipeToUser($user, $completed, ['completed_at' => now()]);

    $scores = app(RecipeAffinity::class)->scoresFor($user);

    expect($scores[$favorited->id])->toBe(5);
    expect($scores[$completed->id])->toBe(1);
    expect($scores[$favorited->id])->toBeGreaterThan($scores[$completed->id]);
});

test('replaced recipe scores below baseline', function () {
    $user = makeAffinityUser();
    $replaced = makeRecipe();
    $neutral = makeRecipe();

    $meal = serveRecipeToUser($user, $replaced);
    $meal->delete();

    serveRecipeToUser($user, $neutral, ['completed_at' => now()]);

    $scores = app(RecipeAffinity::class)->scoresFor($user);

    expect($scores[$replaced->id])->toBe(-3);
    expect($scores[$neutral->id])->toBe(1);
    expect($scores[$replaced->id])->toBeLessThan(0);
});

test('favorite bypasses cooldown, recently served non-favorite does not', function () {
    $user = makeAffinityUser();
    $favorited = makeRecipe();
    $neutralRecent = makeRecipe();

    serveRecipeToUser($user, $favorited);
    serveRecipeToUser($user, $neutralRecent);
    RecipeFavorite::create(['user_id' => $user->id, 'recipe_id' => $favorited->id]);

    $cooldown = app(RecipeAffinity::class)->cooldownIds($user);

    expect($cooldown->all())->not->toContain($favorited->id);
    expect($cooldown->all())->toContain($neutralRecent->id);
});

test('completions stack — repeated success boosts score', function () {
    $user = makeAffinityUser();
    $recipe = makeRecipe();

    serveRecipeToUser($user, $recipe, ['completed_at' => now()]);
    serveRecipeToUser($user, $recipe, ['completed_at' => now()]);
    serveRecipeToUser($user, $recipe, ['completed_at' => now()]);

    $scores = app(RecipeAffinity::class)->scoresFor($user);

    expect($scores[$recipe->id])->toBe(3);
});

test('cold-start user has no scores and no cooldown', function () {
    $user = makeAffinityUser();

    $scores = app(RecipeAffinity::class)->scoresFor($user);
    $cooldown = app(RecipeAffinity::class)->cooldownIds($user);

    expect($scores)->toBeEmpty();
    expect($cooldown)->toBeEmpty();
});
