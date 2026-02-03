<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

test('user can delete their own meal', function () {
    // Arrange: Create user with meal
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 500,
        'total_protein_g' => 30,
        'total_carbs_g' => 50,
        'total_fat_g' => 20,
    ]);

    $meal = Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Breakfast Meal',
        'type' => 'breakfast',
        'calories' => 500,
        'protein_g' => 30,
        'carbs_g' => 50,
        'fat_g' => 20,
    ]);

    // Act: Delete the meal
    $response = deleteJson("/api/v2/meals/{$meal->id}");

    // Assert: Check response and soft delete
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Meal deleted successfully',
        ])
        ->assertJsonStructure([
            'message',
            'deleted_at',
        ]);

    assertSoftDeleted('meals', [
        'id' => $meal->id,
    ]);
});

test('user cannot delete meal belonging to another user', function () {
    // Arrange: Create two users
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    Sanctum::actingAs($otherUser);

    $plan = Plan::factory()->create([
        'user_id' => $owner->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 500,
        'total_protein_g' => 30,
        'total_carbs_g' => 50,
        'total_fat_g' => 20,
    ]);

    $meal = Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Owner Breakfast',
        'type' => 'breakfast',
        'calories' => 500,
        'protein_g' => 30,
        'carbs_g' => 50,
        'fat_g' => 20,
    ]);

    // Act: Try to delete the meal as another user
    $response = deleteJson("/api/v2/meals/{$meal->id}");

    // Assert: Should be forbidden
    $response->assertStatus(403);

    // Meal should still exist (not deleted)
    expect($meal->fresh())->not->toBeNull();
});

test('deleting non-existent meal returns 404', function () {
    // Arrange: Create authenticated user
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Act: Try to delete non-existent meal
    $response = deleteJson('/api/v2/meals/99999');

    // Assert: Should return 404
    $response->assertNotFound();
});

test('unauthenticated user cannot delete meal', function () {
    // Arrange: Create meal without authentication
    $user = User::factory()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 500,
        'total_protein_g' => 30,
        'total_carbs_g' => 50,
        'total_fat_g' => 20,
    ]);

    $meal = Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Breakfast',
        'type' => 'breakfast',
        'calories' => 500,
        'protein_g' => 30,
        'carbs_g' => 50,
        'fat_g' => 20,
    ]);

    // Act: Try to delete without authentication
    $response = deleteJson("/api/v2/meals/{$meal->id}");

    // Assert: Should be unauthorized
    $response->assertStatus(401);

    // Meal should still exist
    expect($meal->fresh())->not->toBeNull();
});

test('deleted meal uses soft delete and includes timestamp', function () {
    // Arrange
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => now()->format('Y-m-d'),
        'status' => 'generated',
        'total_calories' => 500,
        'total_protein_g' => 30,
        'total_carbs_g' => 50,
        'total_fat_g' => 20,
    ]);

    $meal = Meal::create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Test Meal',
        'type' => 'lunch',
        'calories' => 500,
        'protein_g' => 30,
        'carbs_g' => 50,
        'fat_g' => 20,
    ]);

    // Act
    $response = deleteJson("/api/v2/meals/{$meal->id}");

    // Assert: Response contains deleted_at timestamp
    $response->assertStatus(200);
    $deletedAt = $response->json('deleted_at');
    expect($deletedAt)->not->toBeNull();

    // Verify soft delete in database
    $deletedMeal = Meal::withTrashed()->find($meal->id);
    expect($deletedMeal->deleted_at)->not->toBeNull();
    expect($deletedMeal->trashed())->toBeTrue();
});

