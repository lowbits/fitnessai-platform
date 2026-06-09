<?php

use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Enums\CookingPreference;
use App\Enums\MealVariety;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPrompt(array $profileOverrides = [], ?Carbon $date = null, int $dayNumber = 1): string
{
    $profile = UserProfile::factory()->create($profileOverrides);
    $profile->load('user');

    $prompt = new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: $dayNumber,
        date: $date ?? Carbon::parse('2026-06-09'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
    );

    return (string) $prompt;
}

test('prompt includes food dislikes', function () {
    $output = createPrompt(['food_dislikes' => ['pork', 'mushrooms']]);

    expect($output)->toContain('NEVER use these ingredients: pork, mushrooms');
});

test('prompt excludes dislikes line when empty', function () {
    $output = createPrompt(['food_dislikes' => []]);

    expect($output)->not->toContain('NEVER use');
});

test('prompt includes cooking constraint for quick', function () {
    $output = createPrompt(['cooking_preference' => CookingPreference::QUICK]);

    expect($output)->toContain('max 15min total per meal');
});

test('prompt includes cooking constraint for elaborate', function () {
    $output = createPrompt(['cooking_preference' => CookingPreference::ELABORATE]);

    expect($output)->toContain('enjoys cooking');
});

test('prompt excludes cooking constraint for normal', function () {
    $output = createPrompt(['cooking_preference' => CookingPreference::NORMAL]);

    expect($output)->not->toContain('Cooking:');
});

test('prompt generates only selected meal types', function () {
    $output = createPrompt(['selected_meals' => ['breakfast', 'lunch', 'dinner']]);

    expect($output)
        ->toContain('generate 3 meals: breakfast, lunch, dinner')
        ->not->toContain('Snack:');
});

test('prompt defaults to 4 meals when selected_meals is empty', function () {
    $output = createPrompt(['selected_meals' => null]);

    expect($output)->toContain('generate 4 meals: breakfast, lunch, snack, dinner');
});

test('macro targets redistribute proportionally for selected meals', function () {
    $output = createPrompt(['selected_meals' => ['breakfast', 'dinner']]);

    expect($output)
        ->toContain('Breakfast:')
        ->toContain('Dinner:')
        ->not->toContain('Lunch:')
        ->not->toContain('Snack:');
});

test('prompt includes meal variety hint for low', function () {
    $output = createPrompt(['meal_variety' => MealVariety::LOW]);

    expect($output)->toContain('Variety: low');
});

test('prompt includes meal variety hint for high', function () {
    $output = createPrompt(['meal_variety' => MealVariety::HIGH]);

    expect($output)->toContain('Variety: high');
});

test('prompt excludes variety hint for medium', function () {
    $output = createPrompt(['meal_variety' => MealVariety::MEDIUM]);

    expect($output)->not->toContain('Variety:');
});

test('prompt includes meal prep hint on sunday', function () {
    $sunday = Carbon::parse('2026-06-14'); // a Sunday
    $output = createPrompt(['meal_prep_enabled' => true], $sunday);

    expect($output)->toContain('Meal prep day');
});

test('prompt includes leftover hint on non-prep day', function () {
    $monday = Carbon::parse('2026-06-15');
    $output = createPrompt(['meal_prep_enabled' => true], $monday, dayNumber: 3);

    expect($output)->toContain('Leftovers OK');
});

test('prompt excludes meal prep when disabled', function () {
    $output = createPrompt(['meal_prep_enabled' => false]);

    expect($output)
        ->not->toContain('Meal prep')
        ->not->toContain('Leftovers');
});

test('prompt includes favorite recipe signals', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);

    $recipes = Recipe::factory()->count(2)->create([
        'cuisine' => 'mediterranean',
        'primary_protein' => 'chicken',
    ]);
    $user->favoriteRecipes()->attach($recipes->pluck('id'));
    $user->load('favoriteRecipes');
    $profile->load('user');

    $prompt = new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: 1,
        date: Carbon::parse('2026-06-09'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
    );

    expect((string) $prompt)
        ->toContain('Preferred cuisines: mediterranean')
        ->toContain('Preferred proteins: chicken');
});
