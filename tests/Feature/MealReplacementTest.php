<?php

use App\Jobs\ReplaceMealJob;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('user can replace a meal with a recipe title instruction', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'status' => 'generated',
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Chicken Salad',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 35,
        'fat_g' => 20,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 'Grilled Salmon with Lemon Herb Quinoa',
        ]);

    $response->assertStatus(202);
    $response->assertJsonStructure([
        'message',
        'meal_id',
        'instruction',
    ]);
    $response->assertJson([
        'meal_id' => $meal->id,
        'instruction' => 'Grilled Salmon with Lemon Herb Quinoa',
    ]);

    // Verify job was dispatched
    Queue::assertPushed(ReplaceMealJob::class, function ($job) use ($meal) {
        return $job->meal->id === $meal->id
            && $job->hint === 'Grilled Salmon with Lemon Herb Quinoa';
    });
});

test('user cannot replace a meal without instruction', function () {
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['instruction']);
});

test('user cannot replace a meal they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $plan = Plan::factory()->create(['user_id' => $otherUser->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 'Pan-Seared Salmon with Garlic Asparagus',
        ]);

    $response->assertStatus(403);
});

test('replacement validation limits instruction length', function () {
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $longInstruction = str_repeat('a', 501);
    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => $longInstruction,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['instruction']);
});

test('user can replace breakfast meal with specific recipe', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'breakfast',
        'name' => 'Oatmeal with Berries',
        'calories' => 350,
        'protein_g' => 15,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 'Greek Yogurt Parfait with Granola and Fresh Fruit',
        ]);

    $response->assertStatus(202);
    Queue::assertPushed(ReplaceMealJob::class);
});

test('user can replace dinner meal with specific recipe', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'dinner',
        'name' => 'Beef Stir Fry',
        'calories' => 650,
        'protein_g' => 45,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 'Thai Green Curry with Tofu and Vegetables',
        ]);

    $response->assertStatus(202);
    $response->assertJson([
        'instruction' => 'Thai Green Curry with Tofu and Vegetables',
    ]);
});

test('user can replace snack with specific recipe', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'snack',
        'name' => 'Apple with Peanut Butter',
        'calories' => 200,
        'protein_g' => 8,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 'Protein Smoothie Bowl with Mixed Berries',
        ]);

    $response->assertStatus(202);
    Queue::assertPushed(ReplaceMealJob::class);
});

test('replacement works for meals with special characters in instruction', function () {
    Queue::fake();

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 'Hähnchen-Gemüse-Pfanne mit Reis & Kräutern',
        ]);

    $response->assertStatus(202);
    $response->assertJson([
        'instruction' => 'Hähnchen-Gemüse-Pfanne mit Reis & Kräutern',
    ]);
});

test('unauthenticated user cannot replace meal', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->postJson(route('meals.replace', $meal), [
        'instruction' => 'Some Recipe',
    ]);

    $response->assertStatus(401);
});

test('instruction must be a string', function () {
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => 12345,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['instruction']);
});

test('empty string instruction is rejected', function () {
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.replace', $meal), [
            'instruction' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['instruction']);
});

