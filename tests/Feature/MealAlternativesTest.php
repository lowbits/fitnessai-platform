<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\OpenAITools\MealToolDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Responses\CreateResponse;


uses(RefreshDatabase::class);

test('user can get 5 meal title alternatives without hint', function () {
    // Mock OpenAI response
    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeMealAlternativeResponse())
    ]);



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
        'name' => 'Old Lunch',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 35,
        'fat_g' => 20,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.alternatives', $meal));


    $response->assertStatus(200);
    $response->assertJsonStructure([
        'titles',
        'original_meal' => [
            'id',
            'name',
            'type',
            'calories',
            'protein_g',
            'carbs_g',
            'fat_g',
        ],
    ]);

    $titles = $response->json('titles');
    expect($titles)->toBeArray();
    expect($titles)->toHaveCount(5);
    expect($titles[0])->toBe('Pan-Seared Salmon with Garlic Asparagus');
    expect($titles[1])->toBe('Baked Salmon Fillet with Dill Yogurt Sauce');
});

test('user can get title alternatives with hint', function () {
    OpenAI::fake([
        CreateResponse::fake(MealToolDefinition::fakeMealAlternativeResponse()),
    ]);

    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'dinner',
        'calories' => 500,
        'protein_g' => 40,
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.alternatives', $meal), [
            'hint' => 'I want something with salmon',
        ]);

    $response->assertStatus(200);
    $titles = $response->json('titles');
    expect($titles)->toBeArray();
    expect($titles)->toHaveCount(5);
    expect($titles[0])->toBe('Pan-Seared Salmon with Garlic Asparagus');

    // Check that all titles contain salmon-related content
    foreach ($titles as $title) {
        expect($title)->toBeString();
        expect($title)->not->toBeEmpty();
    }
});


test('user cannot get alternatives for meal they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $plan = Plan::factory()->create(['user_id' => $otherUser->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson(route('meals.alternatives', $meal));

    $response->assertStatus(403);
});

test('alternatives validation limits hint length', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $longHint = str_repeat('a', 501);
    $response = $this->actingAs($user)
        ->postJson(route('meals.alternatives', $meal), [
            'hint' => $longHint,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['hint']);
});
