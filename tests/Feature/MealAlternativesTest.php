<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Responses\CreateResponse;


uses(RefreshDatabase::class);

test('user can get 5 meal title alternatives without hint', function () {
    // Mock OpenAI response
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'tool_calls' => [
                            [
                                'id' => 'call_123',
                                'type' => 'function',
                                'function' => [
                                    'name' => 'provide_meal_titles',
                                    'arguments' => json_encode([
                                        'titles' => [
                                            'Grilled Salmon with Lemon Herb Quinoa',
                                            'Chicken Teriyaki Stir Fry with Brown Rice',
                                            'Turkey and Avocado Power Bowl',
                                            'Tuna Poke Bowl with Edamame',
                                            'Beef and Broccoli with Cauliflower Rice',
                                        ],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
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
        ->postJson("/api/v2/meals/{$meal->id}/alternatives");


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
    expect($titles[0])->toBe('Grilled Salmon with Lemon Herb Quinoa');
    expect($titles[1])->toBe('Chicken Teriyaki Stir Fry with Brown Rice');
})->todo("Integrate feature");

test('user can get title alternatives with hint', function () {
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'tool_calls' => [
                            [
                                'id' => 'call_456',
                                'type' => 'function',
                                'function' => [
                                    'name' => 'provide_meal_titles',
                                    'arguments' => json_encode([
                                        'titles' => [
                                            'Pan-Seared Salmon with Garlic Asparagus',
                                            'Baked Salmon Fillet with Dill Yogurt Sauce',
                                            'Salmon Teriyaki Bowl with Edamame',
                                            'Grilled Salmon Caesar Salad',
                                            'Honey Mustard Glazed Salmon with Quinoa',
                                        ],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'dinner',
        'calories' => 500,
        'protein_g' => 40,
    ]);

    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/alternatives", [
            'hint' => 'I want something with salmon',
        ]);

    $response->assertStatus(200);
    $titles = $response->json('titles');
    expect($titles)->toBeArray();
    expect($titles)->toHaveCount(5);
    // Check that all titles are non-empty strings
    foreach ($titles as $title) {
        expect($title)->toBeString();
        expect($title)->not->toBeEmpty();
    }
})->skip('Uses real OpenAI API - OpenAI::fake() does not work with Chat Completion');

test('user cannot get alternatives for meal they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $plan = Plan::factory()->create(['user_id' => $otherUser->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/alternatives");

    $response->assertStatus(403);
});

test('alternatives validation limits hint length', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);
    $plan = Plan::factory()->create(['user_id' => $user->id]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id]);
    $meal = Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $longHint = str_repeat('a', 501);
    $response = $this->actingAs($user)
        ->postJson("/api/v2/meals/{$meal->id}/alternatives", [
            'hint' => $longHint,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['hint']);
});

