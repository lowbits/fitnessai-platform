<?php

use App\Models\CalorieTracking;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can see tracked calories when fetching day plan', function () {
    $user = User::factory()->create();

    // Create an active plan
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);


    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
    ]);

    // Create meal plan for today
    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
        'total_calories' => 2000,
        'total_protein_g' => 150,
        'total_carbs_g' => 200,
        'total_fat_g' => 60,
    ]);



    // Create meals
    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Breakfast Bowl',
        'type' => 'breakfast',
        'calories' => 500,
    ]);

    // Track some calories for today
    $tracking = CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $meal->id,
        'meal_name' => $meal->name,
        'tracked_date' => $date,
        'calories' => 450.00,
        'protein_g' => 25.0,
        'carbs_g' => 50.0,
        'fat_g' => 15.0,
    ]);

    // Mark meal as completed
    $meal->update(['completed_at' => now()]);

    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => null,
        'tracked_date' => $date,
        'calories' => 300.00,
        'protein_g' => 10.0,
        'carbs_g' => 40.0,
        'fat_g' => 10.0,
        'meal_name' => 'Snack',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$date}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'plan_id',
            'date',
            'meals' => [
                '*' => [
                    'id',
                    'name',
                    'type',
                    'calories',
                    'is_completed',
                    'completed_at',
                ],
            ],
            'daily_totals',
            'tracked_calories' => [
                'entries' => [
                    '*' => [
                        'id',
                        'meal_id',
                        'meal_name',
                        'calories',
                        'protein_g',
                        'carbs_g',
                        'fat_g',
                        'notes',
                        'tracked_at',
                    ],
                ],
                'totals' => [
                    'calories',
                    'protein_g',
                    'carbs_g',
                    'fat_g',
                ],
                'count',
            ],
        ])
        ->assertJson([
            'tracked_calories' => [
                'totals' => [
                    'calories' => 750.0,
                    'protein_g' => 35.0,
                    'carbs_g' => 90.0,
                    'fat_g' => 25.0,
                ],
                'count' => 2,
            ],
        ]);

    // Verify entries are present
    expect($response->json('tracked_calories.entries'))->toHaveCount(2);
    expect($response->json('tracked_calories.entries.0.meal_name'))->toBe('Breakfast Bowl');
    expect($response->json('tracked_calories.entries.1.meal_name'))->toBe('Snack');

    // Verify the tracked meal is marked as completed
    $meals = $response->json('meals');
    $trackedMeal = collect($meals)->firstWhere('id', $meal->id);
    expect($trackedMeal['is_completed'])->toBe(true);
    expect($trackedMeal['completed_at'])->not->toBeNull();
});

test('day plan shows empty tracked calories when nothing is tracked', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
    ]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$date}");

    $response->assertStatus(200)
        ->assertJson([
            'tracked_calories' => [
                'entries' => [],
                'totals' => [
                    'calories' => 0.0,
                    'protein_g' => 0.0,
                    'carbs_g' => 0.0,
                    'fat_g' => 0.0,
                ],
                'count' => 0,
            ],
        ]);
});

test('tracked calories only show for requested date', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $today = now()->startOfDay();
    $yesterday = now()->subDay()->startOfDay();
    $dayNumber = $plan->start_date->startOfDay()->diffInDays($today) + 1;

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $today->format('Y-m-d'),
    ]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $today->format('Y-m-d'),
        'status' => 'generated',
    ]);

    // Track calories for today
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'tracked_date' => $today->format('Y-m-d'),
        'calories' => 500.00,
    ]);

    // Track calories for yesterday
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'tracked_date' => $yesterday->format('Y-m-d'),
        'calories' => 800.00,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$today->format('Y-m-d')}");

    $response->assertStatus(200)
        ->assertJson([
            'tracked_calories' => [
                'totals' => [
                    'calories' => 500.0,
                ],
                'count' => 1,
            ],
        ]);
});

test('tracked calories reference is kept when meal is deleted', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now(),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => $date,
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => $date,
        'status' => 'generated',
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Deleted Meal',
        'type' => 'lunch',
    ]);

    // Track the meal
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $meal->id,
        'tracked_date' => $date,
        'calories' => 600.00,
        'protein_g' => 30.0,
    ]);

    // Delete the meal
    $meal->delete();

    // Fetch day plan
    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$date}");

    $response->assertStatus(200);

    // Verify tracking is still there, but meal_id is null
    expect($response->json('tracked_calories.count'))->toBe(1);
    expect($response->json('tracked_calories.entries.0.meal_id'))->toBeNull();
    expect($response->json('tracked_calories.totals.calories'))->toBe(600);
    expect($response->json('tracked_calories.totals.protein_g'))->toBe(30);
});

test('meals show is_completed when tracked', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
    ]);

    // Create 3 meals
    $breakfast = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Breakfast',
        'type' => 'breakfast',
        'calories' => 500,
        'completed_at' => now()
    ]);

    $lunch = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Lunch',
        'type' => 'lunch',
        'calories' => 700,
        'completed_at' => now()
    ]);

    $dinner = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Dinner',
        'type' => 'dinner',
        'calories' => 600
    ]);

    // Track only breakfast and lunch
    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $breakfast->id,
        'tracked_date' => $date,
        'calories' => 500.00,
    ]);


    CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $lunch->id,
        'tracked_date' => $date,
        'calories' => 700.00,
    ]);


    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/v2/plan/day/{$date}");

    $response->assertStatus(200);

    $meals = $response->json('meals');

    // Find each meal and check is_completed
    $breakfastMeal = collect($meals)->firstWhere('id', $breakfast->id);
    $lunchMeal = collect($meals)->firstWhere('id', $lunch->id);
    $dinnerMeal = collect($meals)->firstWhere('id', $dinner->id);

    expect($breakfastMeal['is_completed'])->toBe(true);
    expect($lunchMeal['is_completed'])->toBe(true);
    expect($dinnerMeal['is_completed'])->toBe(false);
});

test('tracking a meal automatically sets completed_at', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Breakfast',
        'type' => 'breakfast',
        'calories' => 500,
        'completed_at' => null,
    ]);

    expect($meal->completed_at)->toBeNull();

    // Track the meal
    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'meal_id' => $meal->id,
            'tracked_date' => $date,
            'calories' => 500.00,
        ]);

    $response->assertStatus(201);

    // Verify completed_at was set
    $meal->refresh();
    expect($meal->completed_at)->not->toBeNull();
});

test('deleting tracking resets completed_at', function () {
    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => now()->subDays(5),
        'duration_days' => 90,
    ]);

    $date = now()->format('Y-m-d');
    $dayNumber = $plan->start_date->startOfDay()->diffInDays(now()->startOfDay()) + 1;

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'date' => $date,
        'status' => 'generated',
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Breakfast',
        'type' => 'breakfast',
        'calories' => 500,
        'completed_at' => now(),
    ]);

    $tracking = CalorieTracking::factory()->create([
        'user_id' => $user->id,
        'meal_id' => $meal->id,
        'tracked_date' => $date,
        'calories' => 500.00,
    ]);

    expect($meal->completed_at)->not->toBeNull();

    // Delete the tracking
    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v2/track/calories/{$tracking->id}");

    $response->assertStatus(200);

    // Verify completed_at was reset
    $meal->refresh();
    expect($meal->completed_at)->toBeNull();
});

