<?php

use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('user can replace a meal without hint', function () {
    Queue::fake();

    // Create user with profile and plan
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'generation_completed_at' => now(),
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
        'date' => today(),
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
    ]);

    // Make authenticated request to replace meal
    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/replace");

    $response->assertStatus(202);
    $response->assertJson([
        'message' => 'Meal replacement is being generated',
    ]);

    // Assert job was dispatched
    Queue::assertPushed(ReplaceMealJob::class, function ($job) use ($meal) {
        return $job->meal->id === $meal->id
            && $job->hint === null;
    });
});

test('user can replace a meal with hint', function () {
    Queue::fake();

    // Create user with profile and plan
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'generation_completed_at' => now(),
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
        'date' => today(),
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'dinner',
        'name' => 'Old Dinner',
    ]);

    // Make authenticated request with hint
    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/replace", [
            'hint' => 'I want something with salmon',
        ]);

    $response->assertStatus(202);
    $response->assertJson([
        'message' => 'Meal replacement is being generated',
    ]);

    // Assert job was dispatched with hint
    Queue::assertPushed(ReplaceMealJob::class, function ($job) use ($meal) {
        return $job->meal->id === $meal->id
            && $job->hint === 'I want something with salmon';
    });
});

test('user cannot replace meal they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $plan = Plan::factory()->create(['user_id' => $otherUser->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/replace");

    $response->assertStatus(403);
    $response->assertJson([
        'error' => 'Unauthorized',
    ]);
});

test('replacing non-existent meal returns 404', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v2/meals/99999/replace');

    $response->assertStatus(404);
    // The response format may vary (custom vs Laravel's ModelNotFoundException)
    // but 404 status is what matters
});

test('hint validation limits length', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    // Try with excessively long hint
    $longHint = str_repeat('a', 501);
    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/replace", [
            'hint' => $longHint,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['hint']);
});

test('user must be authenticated to replace meal', function () {
    $meal = Meal::factory()->create();

    $response = $this->postJson("/api/v2/meals/{$meal->id}/replace");

    $response->assertStatus(401);
});

