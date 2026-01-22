<?php

/**
 * Integration tests for ReplaceMealJob
 *
 * ⚠️ IMPORTANT: These tests are SKIPPED by default ⚠️
 *
 * Why? The OpenAI PHP SDK uses its own HTTP client (Guzzle),
 * so Laravel's Http::fake() doesn't work. These tests would make
 * real API calls, costing money and taking time.
 *
 * To run manually (will cost API tokens):
 * php artisan test --group=integration
 *
 * For CI/CD, always exclude:
 * php artisan test --exclude-group=integration
 */

use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('meal replacement job updates meal with new data', function () {
    // Create test data
    $user = User::factory()->create(['locale' => 'en']);
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
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

    // Execute the job (makes real API call!)
    $job = new ReplaceMealJob($meal, 'I want something with salmon');
    $job->handle();

    // Refresh meal from database
    $meal->refresh();

    // Assert meal was updated (not replaced)
    expect($meal->id)->toBe($originalMealId);
    expect($meal->name)->not->toBe('Old Lunch');
    expect($meal->name)->not->toBeEmpty();
    expect($meal->description)->not->toBeNull();
    expect($meal->calories)->toBeGreaterThan(0);
    expect($meal->protein_g)->toBeGreaterThan(0);
    expect($meal->carbs_g)->toBeGreaterThan(0);
    expect($meal->fat_g)->toBeGreaterThan(0);
    expect($meal->ingredients)->toBeArray();
    expect($meal->instructions)->toBeArray();
    expect($meal->ingredients)->not->toBeEmpty();
    expect($meal->instructions)->not->toBeEmpty();

    // Assert meal plan totals were updated
    $mealPlan->refresh();
    expect($mealPlan->total_calories)->toBeGreaterThan(0);
    expect($mealPlan->total_protein_g)->toBeGreaterThan(0);
})->group('integration')->skip('Requires OpenAI API key and makes real API calls');

test('meal replacement job without hint generates alternative', function () {
    $user = User::factory()->create(['locale' => 'de']);
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'breakfast',
        'name' => 'Old Breakfast',
    ]);

    // Execute job without hint (makes real API call!)
    $job = new ReplaceMealJob($meal, null);
    $job->handle();

    $meal->refresh();

    expect($meal->name)->not->toBe('Old Breakfast');
    expect($meal->name)->not->toBeEmpty();
    expect($meal->calories)->toBeGreaterThan(0);
})->group('integration')->skip('Requires OpenAI API key and makes real API calls');

test('meal replacement job with multiple meals updates only target meal', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'total_calories' => 1500,
    ]);

    // Create multiple meals
    $breakfast = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'breakfast',
        'name' => 'Breakfast',
        'calories' => 400,
        'protein_g' => 30,
        'carbs_g' => 40,
        'fat_g' => 10,
    ]);

    $lunch = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Lunch',
        'calories' => 600,
        'protein_g' => 50,
        'carbs_g' => 60,
        'fat_g' => 20,
    ]);

    $dinner = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'dinner',
        'name' => 'Old Dinner',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 50,
        'fat_g' => 15,
    ]);

    // Replace only dinner (makes real API call!)
    $job = new ReplaceMealJob($dinner, null);
    $job->handle();

    // Refresh meals
    $breakfast->refresh();
    $lunch->refresh();
    $dinner->refresh();

    // Assert only dinner was changed
    expect($breakfast->name)->toBe('Breakfast');
    expect($lunch->name)->toBe('Lunch');
    expect($dinner->name)->not->toBe('Old Dinner');
    expect($dinner->name)->not->toBeEmpty();

    // Assert meal plan totals were recalculated
    $mealPlan->refresh();
    expect($mealPlan->total_calories)->toBeGreaterThan(0);
})->group('integration')->skip('Requires OpenAI API key and makes real API calls');

