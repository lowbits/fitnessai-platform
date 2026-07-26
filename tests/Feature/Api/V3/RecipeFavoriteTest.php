<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('guest cannot access favorite endpoints', function () {
    $recipe = Recipe::factory()->create();

    getJson('/api/v3/recipes/favorites')->assertUnauthorized();
    postJson("/api/v3/recipes/{$recipe->id}/favorite")->assertUnauthorized();
    deleteJson("/api/v3/recipes/{$recipe->id}/favorite")->assertUnauthorized();
});

test('user can favorite a recipe', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $recipe = Recipe::factory()->create();

    postJson("/api/v3/recipes/{$recipe->id}/favorite")
        ->assertCreated();

    expect($user->favoriteRecipes)->toHaveCount(1)
        ->and($user->favoriteRecipes->first()->id)->toBe($recipe->id);
});

test('favoriting same recipe twice does not duplicate', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $recipe = Recipe::factory()->create();

    postJson("/api/v3/recipes/{$recipe->id}/favorite")->assertCreated();
    postJson("/api/v3/recipes/{$recipe->id}/favorite")->assertCreated();

    expect($user->favoriteRecipes)->toHaveCount(1);
});

test('user can unfavorite a recipe', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $recipe = Recipe::factory()->create();
    $user->favoriteRecipes()->attach($recipe);

    deleteJson("/api/v3/recipes/{$recipe->id}/favorite")
        ->assertOk();

    expect($user->fresh()->favoriteRecipes)->toHaveCount(0);
});

test('user can list their favorites', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $recipes = Recipe::factory()->count(3)->create();
    $user->favoriteRecipes()->attach($recipes->pluck('id'));

    getJson('/api/v3/recipes/favorites')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('favorites list does not include other users recipes', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Sanctum::actingAs($user);

    $recipe = Recipe::factory()->create();
    $other->favoriteRecipes()->attach($recipe);

    getJson('/api/v3/recipes/favorites')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
