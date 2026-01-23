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

test('meal replacement job creates new meal and soft deletes old one', function () {
    // Create test data
    $user = User::factory()->en()->withProfile()->create();
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

    $originalMeal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 30,
        'fat_g' => 20,
    ]);

    $originalMealId = $originalMeal->id;

    // Execute the job (makes real API call!)
    $job = new ReplaceMealJob($originalMeal, 'I want something with salmon');
    $job->handle();

    // Refresh old meal from database
    $originalMeal->refresh();

    // Assert old meal was soft deleted
    expect($originalMeal->deleted_at)->not->toBeNull();
    expect($originalMeal->trashed())->toBeTrue();

    // Assert new meal was created
    $newMeal = Meal::where('meal_plan_id', $mealPlan->id)
        ->where('type', 'lunch')
        ->whereNull('deleted_at')
        ->first();

    expect($newMeal)->not->toBeNull();
    expect($newMeal->id)->not->toBe($originalMealId);
    expect($newMeal->name)->not->toBe('Old Lunch');
    expect($newMeal->name)->not->toBe('Generating...');
    expect($newMeal->name)->not->toBeEmpty();
    expect($newMeal->description)->not->toBeNull();
    expect($newMeal->status)->toBe('generated');  // Check meal status
    expect($newMeal->calories)->toBeGreaterThan(0);
    expect($newMeal->protein_g)->toBeGreaterThan(0);
    expect($newMeal->carbs_g)->toBeGreaterThan(0);
    expect($newMeal->fat_g)->toBeGreaterThan(0);
    expect($newMeal->ingredients)->toBeArray();
    expect($newMeal->instructions)->toBeArray();
    expect($newMeal->ingredients)->not->toBeEmpty();
    expect($newMeal->instructions)->not->toBeEmpty();

    // Assert meal plan totals were updated and status is "generated"
    $mealPlan->refresh();
    expect($mealPlan->status)->toBe('generated');
    expect($mealPlan->total_calories)->toBeGreaterThan(0);
    expect($mealPlan->total_protein_g)->toBeGreaterThan(0);
})->group('integration')->skip('Requires OpenAI API key and makes real API calls');;

test('meal replacement job without hint generates alternative', function () {
    $user = User::factory()->create(['locale' => 'de']);
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
    ]);

    $originalMeal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'breakfast',
        'name' => 'Old Breakfast',
    ]);

    // Execute job without hint (makes real API call!)
    $job = new ReplaceMealJob($originalMeal, null);
    $job->handle();

    $originalMeal->refresh();

    // Assert old meal was deleted
    expect($originalMeal->trashed())->toBeTrue();

    // Assert new meal was created
    $newMeal = Meal::where('meal_plan_id', $mealPlan->id)
        ->where('type', 'breakfast')
        ->whereNull('deleted_at')
        ->first();

    expect($newMeal)->not->toBeNull();
    expect($newMeal->name)->not->toBe('Old Breakfast');
    expect($newMeal->name)->not->toBeEmpty();
    expect($newMeal->status)->toBe('generated');  // Check meal status
    expect($newMeal->calories)->toBeGreaterThan(0);

    // Assert meal plan status is "generated"
    $mealPlan->refresh();
    expect($mealPlan->status)->toBe('generated');
})->group('integration')->skip('Requires OpenAI API key and makes real API calls');

test('meal replacement job with multiple meals updates only target meal', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'total_calories' => 1500,
        'status' => 'generated',
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

    // Assert only dinner was changed (soft deleted)
    expect($breakfast->name)->toBe('Breakfast');
    expect($breakfast->trashed())->toBeFalse();
    expect($lunch->name)->toBe('Lunch');
    expect($lunch->trashed())->toBeFalse();
    expect($dinner->trashed())->toBeTrue();

    // Assert new dinner meal was created
    $newDinner = Meal::where('meal_plan_id', $mealPlan->id)
        ->where('type', 'dinner')
        ->whereNull('deleted_at')
        ->first();

    expect($newDinner)->not->toBeNull();
    expect($newDinner->name)->not->toBe('Old Dinner');
    expect($newDinner->name)->not->toBeEmpty();
    expect($newDinner->status)->toBe('generated');  // Check meal status

    // Assert meal plan totals were recalculated and status is "generated"
    $mealPlan->refresh();
    expect($mealPlan->status)->toBe('generated');
    expect($mealPlan->total_calories)->toBeGreaterThan(0);
})->group('integration')->skip('Requires OpenAI API key and makes real API calls');

