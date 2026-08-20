<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;

/**
 * Live evals — these hit the REAL model (OpenAI), so they are off by default.
 * Turn them on while implementing a chat feature:
 *
 *   MONA_EVAL=1 php artisan test --group=eval
 *
 * Each case is a playbook row: a user sentence -> the expected API result
 * (which widget/tool the mobile client should receive back).
 */
function evalMealForUser(): array
{
    $user = User::factory()->withProfile()->create();
    $user->update(['locale' => 'de']);

    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'status' => 'generated']);
    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Ofenhähnchen mit Reis und Brokkoli',
        'calories' => 620,
        'protein_g' => 50,
        'carbs_g' => 60,
        'fat_g' => 20,
    ]);

    return [$user, $meal];
}

test('a German swap request returns a meal_alternatives widget', function () {
    [$user, $meal] = evalMealForUser();

    $response = $this->actingAs($user)->postJson(route('v3.coach.messages'), [
        'message' => 'Ersetze mein Mittagessen',
        'context' => ['type' => 'meal_replace', 'meal_id' => $meal->id],
    ]);

    $response->assertStatus(200);

    $widgets = collect($response->json('message.parts'))
        ->where('type', 'widget')
        ->pluck('name');

    expect($widgets)->toContain('meal_alternatives');
})->group('eval')->skip(fn () => ! getenv('MONA_EVAL'), 'Live eval — set MONA_EVAL=1 to run.');
