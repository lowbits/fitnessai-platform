<?php

use App\Ai\Tools\ProposeMealAlternativesTool;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\Recipe;
use App\Models\User;
use App\Services\Recipe\RecipeAffinity;
use App\Services\Recipe\RecipeFinder;
use Laravel\Ai\Tools\Request;

function ownedMeal(): array
{
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active', 'start_date' => today()]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'date' => today()]);
    $meal = Meal::factory()->for($mealPlan, 'mealPlan')->create([
        'type' => 'lunch',
        'calories' => 600,
        'completed_at' => null,
    ]);

    return [$user, $meal];
}

function proposeTool(User $user, $candidates): ProposeMealAlternativesTool
{
    $finder = Mockery::mock(RecipeFinder::class);
    $finder->shouldReceive('findCandidates')->andReturn(collect($candidates));

    $affinity = Mockery::mock(RecipeAffinity::class);
    $affinity->shouldReceive('scoresFor')->andReturn(collect());

    return new ProposeMealAlternativesTool($user, $finder, $affinity);
}

test('no matching alternatives returns a typed no_alternatives signal', function () {
    [$user, $meal] = ownedMeal();

    $result = json_decode(proposeTool($user, [])->handle(new Request([
        'meal_id' => $meal->id,
        'wish' => 'Gambas mit Nudeln',
    ])), true);

    expect($result['error'] ?? null)->toBe('no_alternatives');
});

test('alternatives are returned as a meal_alternatives widget', function () {
    [$user, $meal] = ownedMeal();
    $recipe = Recipe::factory()->create(['calories' => 580]);

    $result = json_decode(proposeTool($user, [$recipe])->handle(new Request([
        'meal_id' => $meal->id,
    ])), true);

    expect($result['widget'] ?? null)->toBe('meal_alternatives');
    expect($result['data']['cards'])->toHaveCount(1);
});
