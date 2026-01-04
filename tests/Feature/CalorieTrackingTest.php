<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\CalorieTracking;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $this->user->id]);
    $this->mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $this->meal = Meal::factory()->create(['meal_plan_id' => $this->mealPlan->id]);
});

test('user can track calories without meal', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 500.50,
            'protein_g' => 25.5,
            'carbs_g' => 60.0,
            'fat_g' => 15.5,
            'meal_name' => 'Breakfast Bowl',
            'notes' => 'Homemade oatmeal with fruits',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'meal_id',
                'tracked_date',
                'calories',
                'protein_g',
                'carbs_g',
                'fat_g',
                'meal_name',
                'notes',
                'meal',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'data' => [
                'calories' => 500.50,
                'protein_g' => 25.5,
                'carbs_g' => 60.0,
                'fat_g' => 15.5,
                'meal_name' => 'Breakfast Bowl',
                'notes' => 'Homemade oatmeal with fruits',
                'meal' => null,
            ],
        ]);

    $this->assertDatabaseHas('calorie_trackings', [
        'user_id' => $this->user->id,
        'meal_id' => null,
        'calories' => 500.50,
        'meal_name' => 'Breakfast Bowl',
    ]);
});

test('user can track calories with meal reference', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'meal_id' => $this->meal->id,
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 750.00,
            'protein_g' => 35.0,
            'carbs_g' => 80.0,
            'fat_g' => 20.0,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'meal_id' => $this->meal->id,
                'calories' => 750.00,
                'protein_g' => 35.0,
                'carbs_g' => 80.0,
                'fat_g' => 20.0,
            ],
        ])
        ->assertJsonPath('data.meal.id', $this->meal->id);

    $this->assertDatabaseHas('calorie_trackings', [
        'user_id' => $this->user->id,
        'meal_id' => $this->meal->id,
        'calories' => 750.00,
    ]);
});

test('user can track calories with only required fields', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 300.00,
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'calories' => 300.00,
                'protein_g' => null,
                'carbs_g' => null,
                'fat_g' => null,
                'meal_name' => null,
                'notes' => null,
            ],
        ]);

    $this->assertDatabaseHas('calorie_trackings', [
        'user_id' => $this->user->id,
        'calories' => 300.00,
    ]);
});

test('user can get their calorie trackings', function () {
    CalorieTracking::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'tracked_date' => now()->subDays(1),
    ]);

    CalorieTracking::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'tracked_date' => now(),
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/calories');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'tracked_date',
                    'calories',
                    'protein_g',
                    'carbs_g',
                    'fat_g',
                    'meal_name',
                    'notes',
                    'meal',
                ],
            ],
        ]);
});

test('user can filter calorie trackings by date range', function () {
    // Create trackings for different dates
    CalorieTracking::factory()->create([
        'user_id' => $this->user->id,
        'tracked_date' => now()->subDays(5),
    ]);

    CalorieTracking::factory()->create([
        'user_id' => $this->user->id,
        'tracked_date' => now()->subDays(2),
    ]);

    CalorieTracking::factory()->create([
        'user_id' => $this->user->id,
        'tracked_date' => now(),
    ]);

    $startDate = now()->startOfDay()->subDays(3)->format('Y-m-d');
    $endDate = now()->startOfDay()->format('Y-m-d');

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/track/calories?start_date={$startDate}&end_date={$endDate}");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('user can get a specific calorie tracking', function () {
    $tracking = CalorieTracking::factory()->create([
        'user_id' => $this->user->id,
        'calories' => 600.00,
        'protein_g' => 30.0,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/track/calories/{$tracking->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $tracking->id,
                'calories' => 600.00,
                'protein_g' => 30.0,
            ],
        ]);
});

test('user can update a calorie tracking', function () {
    $tracking = CalorieTracking::factory()->create([
        'user_id' => $this->user->id,
        'calories' => 500.00,
        'protein_g' => 20.0,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/v2/track/calories/{$tracking->id}", [
            'calories' => 550.00,
            'protein_g' => 25.0,
            'notes' => 'Updated notes',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'calories' => 550.00,
                'protein_g' => 25.0,
                'notes' => 'Updated notes',
            ],
        ]);

    $this->assertDatabaseHas('calorie_trackings', [
        'id' => $tracking->id,
        'calories' => 550.00,
        'protein_g' => 25.0,
        'notes' => 'Updated notes',
    ]);
});

test('user cannot access another users calorie tracking', function () {
    $otherUser = User::factory()->create();
    $tracking = CalorieTracking::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/v2/track/calories/{$tracking->id}");

    $response->assertStatus(403);
});

test('calorie tracking requires authentication', function () {
    $response = $this->postJson('/api/v2/track/calories', [
        'tracked_date' => now()->format('Y-m-d'),
        'calories' => 500.00,
    ]);

    $response->assertStatus(401);
});

test('calorie tracking requires valid tracked_date', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => 'invalid-date',
            'calories' => 500.00,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['tracked_date']);
});

test('calorie tracking requires valid calories', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => -10,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['calories']);
});

test('calorie tracking validates macros range', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 500.00,
            'protein_g' => -5,
            'carbs_g' => 100000,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['protein_g', 'carbs_g']);
});

test('user can delete a calorie tracking', function () {
    $tracking = CalorieTracking::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/v2/track/calories/{$tracking->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Calorie tracking deleted successfully',
        ]);

    $this->assertDatabaseMissing('calorie_trackings', [
        'id' => $tracking->id,
    ]);
});

test('user cannot delete another users calorie tracking', function () {
    $otherUser = User::factory()->create();
    $tracking = CalorieTracking::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/v2/track/calories/{$tracking->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('calorie_trackings', [
        'id' => $tracking->id,
    ]);
});

test('multiple calorie entries can be tracked on same day', function () {
    // Track breakfast
    $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 400.00,
            'meal_name' => 'Breakfast',
        ]);

    // Track lunch
    $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 600.00,
            'meal_name' => 'Lunch',
        ]);

    // Track dinner
    $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/v2/track/calories', [
            'tracked_date' => now()->format('Y-m-d'),
            'calories' => 700.00,
            'meal_name' => 'Dinner',
        ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/v2/track/calories');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');

    // Verify all three meals are stored
    $this->assertDatabaseCount('calorie_trackings', 3);
});

