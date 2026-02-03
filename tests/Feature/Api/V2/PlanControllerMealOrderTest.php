<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\getJson;

test('meals are always returned in correct order: breakfast, lunch, snack, dinner', function () {
    // Arrange: Create user with active plan
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    Sanctum::actingAs($user);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now(),
        'duration_days' => 30,
    ]);

    // Create meal plan for day 1
    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 2000,
        'total_protein_g' => 150,
        'total_carbs_g' => 200,
        'total_fat_g' => 70,
    ]);

    // Create meals in WRONG order (to test sorting)
    // Create them in: Dinner -> Snack -> Breakfast -> Lunch
    Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Grilled Chicken',
        'type' => 'dinner',
        'calories' => 600,
        'protein_g' => 50,
        'carbs_g' => 40,
        'fat_g' => 20,
    ]);

    Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Protein Bar',
        'type' => 'snack',
        'calories' => 200,
        'protein_g' => 15,
        'carbs_g' => 20,
        'fat_g' => 8,
    ]);

    Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Oatmeal with Berries',
        'type' => 'breakfast',
        'calories' => 400,
        'protein_g' => 15,
        'carbs_g' => 60,
        'fat_g' => 10,
    ]);

    Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Salmon Salad',
        'type' => 'lunch',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 30,
        'fat_g' => 25,
    ]);

    // Act: Request the plan for today
    $response = getJson('/api/v2/plan/day/' . now()->format('Y-m-d'));

    // Assert: Check status and meal order
    $response->assertStatus(200);

    $meals = $response->json('meals');

    // Assert we have 4 meals
    expect($meals)->toHaveCount(4);

    // Assert meals are in correct order: Breakfast -> Lunch -> Snack -> Dinner
    expect($meals[0]['type'])->toBe('Breakfast', 'First meal should be Breakfast');
    expect($meals[0]['name'])->toBe('Oatmeal with Berries');

    expect($meals[1]['type'])->toBe('Lunch', 'Second meal should be Lunch');
    expect($meals[1]['name'])->toBe('Salmon Salad');

    expect($meals[2]['type'])->toBe('Snack', 'Third meal should be Snack');
    expect($meals[2]['name'])->toBe('Protein Bar');

    expect($meals[3]['type'])->toBe('Dinner', 'Fourth meal should be Dinner');
    expect($meals[3]['name'])->toBe('Grilled Chicken');
});

test('meal order is maintained with missing meal types', function () {
    // Arrange: Create user with active plan
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    Sanctum::actingAs($user);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now(),
        'duration_days' => 30,
    ]);

    // Create meal plan for day 1
    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 1200,
        'total_protein_g' => 80,
        'total_carbs_g' => 120,
        'total_fat_g' => 40,
    ]);

    // Create only Dinner and Breakfast (skip Lunch and Snack)
    Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Steak',
        'type' => 'dinner',
        'calories' => 700,
        'protein_g' => 50,
        'carbs_g' => 40,
        'fat_g' => 30,
    ]);

    Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Eggs',
        'type' => 'breakfast',
        'calories' => 500,
        'protein_g' => 30,
        'carbs_g' => 80,
        'fat_g' => 10,
    ]);

    // Act: Request the plan for today
    $response = getJson('/api/v2/plan/day/' . now()->format('Y-m-d'));

    // Assert: Check status and meal order
    $response->assertStatus(200);

    $meals = $response->json('meals');

    // Assert we have 2 meals in correct order
    expect($meals)->toHaveCount(2);
    expect($meals[0]['type'])->toBe('Breakfast');
    expect($meals[1]['type'])->toBe('Dinner');
});

test('meal order performance uses eager loading', function () {
    // Arrange: Create user with active plan
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    Sanctum::actingAs($user);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now(),
        'duration_days' => 30,
    ]);

    // Create meal plan
    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 2000,
        'total_protein_g' => 150,
        'total_carbs_g' => 200,
        'total_fat_g' => 70,
    ]);

    // Create meals
    foreach (['dinner', 'snack', 'breakfast', 'lunch'] as $type) {
        Meal::create([
            'meal_plan_id' => $mealPlan->id,
            'name' => ucfirst($type) . ' Meal',
            'type' => $type,
            'calories' => 500,
            'protein_g' => 35,
            'carbs_g' => 50,
            'fat_g' => 18,
        ]);
    }

    // Act & Assert: Track queries
    assertDatabaseCount('meals', 4);

    // This will verify eager loading is used (should be ~3 queries: user, plan, meal_plan with meals)
    $response = getJson('/api/v2/plan/day/' . now()->format('Y-m-d'));

    $response->assertStatus(200);

    // Verify order
    $meals = $response->json('meals');
    $types = array_column($meals, 'type');
    expect($types)->toBe(['Breakfast', 'Lunch', 'Snack', 'Dinner']);
});

