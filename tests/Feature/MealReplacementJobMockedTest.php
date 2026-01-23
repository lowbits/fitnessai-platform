<?php

/**
 * Tests for ReplaceMealJob using mocked OpenAI responses
 */

use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\OpenAITools\MealToolDefinition;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Responses\CreateResponse;

uses(RefreshDatabase::class);

test('meal replacement job updates meal with mocked OpenAI response', function () {
    // Mock OpenAI using MealToolDefinition helper
    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeReplaceMealResponse()),
    ]);

    // Create test data
    $user = User::factory()->withProfile()->create(['locale' => 'en']);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
        'total_calories' => 500,
        'total_protein_g' => 40,
        'total_carbs_g' => 30,
        'total_fat_g' => 20,
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 30,
        'fat_g' => 20,
    ]);

    $originalMealId = $meal->id;

    // Execute the job (uses mocked response)
    $job = new ReplaceMealJob($meal, 'I want something with salmon');
    $job->handle();

    // Refresh meal from database
    $meal->refresh();

    // Assert meal was updated with the mocked data
    expect($meal->id)->toBe($originalMealId);
    expect($meal->name)->toBe('Grilled Salmon with Lemon Herb Quinoa');
    expect($meal->description)->toBe('Fresh grilled salmon fillet served with fluffy quinoa infused with lemon and herbs');
    expect($meal->calories)->toBe(520);
    expect($meal->protein_g)->toBe(42);
    expect($meal->carbs_g)->toBe(38);
    expect($meal->fat_g)->toBe(18);
    expect($meal->fiber_g)->toBe(6);
    expect($meal->sugar_g)->toBe(3);
    expect($meal->ingredients)->toBeArray();
    expect($meal->instructions)->toBeArray();
    expect($meal->ingredients)->toHaveCount(5);
    expect($meal->instructions)->toHaveCount(5);
    expect($meal->prep_time_minutes)->toBe(10);
    expect($meal->cook_time_minutes)->toBe(15);
    expect($meal->difficulty)->toBe('Medium');
    expect($meal->tags)->toContain('high-protein');
    expect($meal->allergens)->toContain('fish');

    // Assert meal plan totals were updated
    $mealPlan->refresh();
    expect($mealPlan->total_calories)->toBe(520);
    expect($mealPlan->total_protein_g)->toBe(42);
    expect($mealPlan->total_carbs_g)->toBe(38);
    expect($mealPlan->total_fat_g)->toBe(18);
});

test('meal replacement with custom meal data', function () {
    // Create a custom meal replacement
    $customMeal = [
        'type' => 'breakfast',
        'name' => 'Protein Pancakes',
        'description' => 'Fluffy protein-rich pancakes',
        'calories' => 420,
        'protein_g' => 35,
        'carbs_g' => 45,
        'fat_g' => 10,
        'fiber_g' => 5,
        'sugar_g' => 8,
        'ingredients' => [
            ['name' => 'Protein powder', 'amount' => '30', 'unit' => 'g'],
            ['name' => 'Eggs', 'amount' => '2', 'unit' => 'piece'],
            ['name' => 'Oats', 'amount' => '50', 'unit' => 'g'],
        ],
        'instructions' => [
            'Mix all ingredients',
            'Cook on griddle',
            'Serve with berries',
        ],
        'prep_time_minutes' => 5,
        'cook_time_minutes' => 10,
        'difficulty' => 'Easy',
        'tags' => ['high-protein', 'breakfast'],
        'allergens' => ['eggs', 'dairy'],
    ];

    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeReplaceMealResponse($customMeal)),
    ]);

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
        'total_calories' => 300,
        'total_protein_g' => 20,
        'total_carbs_g' => 30,
        'total_fat_g' => 10,
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'breakfast',
        'name' => 'Old Breakfast',
        'calories' => 300,
        'protein_g' => 20,
        'carbs_g' => 30,
        'fat_g' => 10,
    ]);

    $job = new ReplaceMealJob($meal, null);
    $job->handle();

    $meal->refresh();

    expect($meal->name)->toBe('Protein Pancakes');
    expect($meal->calories)->toBe(420);
    expect($meal->protein_g)->toBe(35);
});

test('meal replacement without hint still works', function () {
    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeReplaceMealResponse()),
    ]);

    $user = User::factory()->withProfile()->create(['locale' => 'de']);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
        'total_calories' => 400,
        'total_protein_g' => 30,
        'total_carbs_g' => 40,
        'total_fat_g' => 15,
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
        'calories' => 400,
        'protein_g' => 30,
        'carbs_g' => 40,
        'fat_g' => 15,
    ]);

    // Execute job without hint
    $job = new ReplaceMealJob($meal, null);
    $job->handle();

    $meal->refresh();

    expect($meal->name)->not->toBe('Old Lunch');
    expect($meal->name)->toBe('Grilled Salmon with Lemon Herb Quinoa');
    expect($meal->calories)->toBeGreaterThan(0);
});

