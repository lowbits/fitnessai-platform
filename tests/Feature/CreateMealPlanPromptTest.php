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

test('low variety scales uniques to meal count', function () {
    // 4 meals/day = 28 slots → ~7 uniques, max 4x
    $output = createPrompt(['meal_variety' => MealVariety::LOW, 'meal_prep_enabled' => false]);

    expect($output)->toContain('VARIETY RULE')->toContain('LOW')->toContain('unique recipes');
});

test('low variety + meal prep on prep day = batch-friendly', function () {
    $sunday = Carbon::parse('2026-06-14');
    $output = createPrompt(['meal_variety' => MealVariety::LOW, 'meal_prep_enabled' => true], $sunday);

    expect($output)->toContain('LOW')->toContain('batch-friendly');
});

test('medium variety with 3 meals produces satisfiable rule', function () {
    // 3 meals/day = 21 slots → 11 uniques, max 2x (11×2=22 ≥ 21)
    $output = createPrompt([
        'meal_variety' => MealVariety::MEDIUM,
        'meal_prep_enabled' => false,
        'selected_meals' => ['breakfast', 'lunch', 'dinner'],
    ]);

    expect($output)
        ->toContain('VARIETY RULE')
        ->toContain('MEDIUM')
        ->toContain('11 unique recipes')
        ->toContain('MUST NOT appear more than 2x');
});

test('medium variety with 4 meals produces satisfiable rule', function () {
    // 4 meals/day = 28 slots → 14 uniques, max 2x (14×2=28 ≥ 28)
    $output = createPrompt(['meal_variety' => MealVariety::MEDIUM, 'meal_prep_enabled' => false]);

    expect($output)->toContain('14 unique recipes')->toContain('more than 2x');
});

test('medium variety + meal prep on prep day includes prep hint', function () {
    $sunday = Carbon::parse('2026-06-14');
    $output = createPrompt(['meal_variety' => MealVariety::MEDIUM, 'meal_prep_enabled' => true], $sunday);

    expect($output)->toContain('MEDIUM')->toContain('batch-friendly');
});

test('high variety includes zero repeats rule', function () {
    $output = createPrompt(['meal_variety' => MealVariety::HIGH, 'meal_prep_enabled' => false]);

    expect($output)->toContain('HIGH')->toContain('zero repeats');
});

test('high variety + meal prep on prep day = diverse components', function () {
    $sunday = Carbon::parse('2026-06-14');
    $output = createPrompt(['meal_variety' => MealVariety::HIGH, 'meal_prep_enabled' => true], $sunday);

    expect($output)->toContain('HIGH')->toContain('diverse components');
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
