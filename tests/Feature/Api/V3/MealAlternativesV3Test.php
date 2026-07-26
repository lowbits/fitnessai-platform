<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\Recipe;
use App\Models\User;
use App\OpenAITools\MealToolDefinition;
use App\Services\Recipe\RecipeFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Responses\CreateResponse;

uses(RefreshDatabase::class);

function mealWithUser(array $overrides = []): array
{
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'status' => 'generated']);
    $meal = Meal::factory()->create(array_merge([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 35,
        'fat_g' => 20,
    ], $overrides));

    return [$user, $meal];
}

test('returns response with three top-level keys: recipe_suggestions, ai_suggestions, original_meal', function () {
    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeMealAlternativeResponse()),
    ]);

    [$user, $meal] = mealWithUser();

    $response = $this->actingAs($user)->postJson(route('v3.meals.alternatives', $meal));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'recipe_suggestions',
        'ai_suggestions',
        'original_meal' => ['id', 'name', 'type', 'calories', 'protein_g', 'carbs_g', 'fat_g'],
    ]);
});

test('cold-start user (no DB recipes) gets only ai_suggestions', function () {
    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeMealAlternativeResponse()),
    ]);

    [$user, $meal] = mealWithUser();

    $response = $this->actingAs($user)->postJson(route('v3.meals.alternatives', $meal));

    expect($response->json('recipe_suggestions'))->toBeArray()->toBeEmpty();
    expect($response->json('ai_suggestions'))->toBeArray()->toHaveCount(5);
    expect($response->json('ai_suggestions.0'))->toHaveKey('title');
});

test('recipe_suggestion has rich shape including image_url and macros', function () {
    [$user, $meal] = mealWithUser();
    $recipe = Recipe::factory()->create([
        'name' => 'Surfaced Bowl',
        'image_full' => 'recipes/test.webp',
        'calories' => 510,
        'protein_g' => 41,
        'carbs_g' => 36,
        'fat_g' => 21,
    ]);

    $this->mock(RecipeFinder::class, function ($mock) use ($recipe) {
        $mock->shouldReceive('findCandidates')
            ->andReturn(collect([$recipe]));
    });

    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeMealAlternativeResponse()),
    ]);

    $response = $this->actingAs($user)->postJson(route('v3.meals.alternatives', $meal));

    $first = $response->json('recipe_suggestions.0');
    expect($first)->toHaveKeys(['id', 'name', 'image_url', 'thumbnail_url', 'calories', 'protein_g', 'carbs_g', 'fat_g']);
    expect($first['name'])->toBe('Surfaced Bowl');
    expect($first['image_url'])->toContain('recipes/test.webp');
});

test('DB recipes fill TARGET; AI fallback fills only the remaining slots', function () {
    [$user, $meal] = mealWithUser();
    $recipes = Recipe::factory()->count(3)->create();

    $this->mock(RecipeFinder::class, function ($mock) use ($recipes) {
        $mock->shouldReceive('findCandidates')
            ->andReturn($recipes);
    });

    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeMealAlternativeResponse()),
    ]);

    $response = $this->actingAs($user)->postJson(route('v3.meals.alternatives', $meal));

    expect($response->json('recipe_suggestions'))->toHaveCount(3);
    expect($response->json('ai_suggestions'))->toHaveCount(2);
});

test('when DB has 5 hits no AI call is made', function () {
    [$user, $meal] = mealWithUser();
    $recipes = Recipe::factory()->count(5)->create();

    $this->mock(RecipeFinder::class, function ($mock) use ($recipes) {
        $mock->shouldReceive('findCandidates')
            ->andReturn($recipes);
    });

    OpenAI::fake();

    $response = $this->actingAs($user)->postJson(route('v3.meals.alternatives', $meal));

    expect($response->json('recipe_suggestions'))->toHaveCount(5);
    expect($response->json('ai_suggestions'))->toBeEmpty();

    OpenAI::assertNothingSent();
});
