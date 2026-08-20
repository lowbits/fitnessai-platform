<?php

use App\Ai\Tools\AddMealTool;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Services\Recipe\MealAlternatives;
use Laravel\Ai\Tools\Request;

/**
 * Mona playbook — add a meal to today. The recipe search is mocked so these
 * assert the tool's decision logic (budget, slot, fill) deterministically.
 */
function addMealFixture(int $goal = 2000, float $eaten = 0.0, array $existingMealTypes = []): array
{
    $user = User::factory()->withProfile()->create();
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'start_date' => today(),
        'daily_calories' => $goal,
    ]);
    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'date' => today(),
    ]);

    foreach ($existingMealTypes as $type) {
        Meal::factory()->for($mealPlan, 'mealPlan')->create(['type' => $type]);
    }

    if ($eaten > 0) {
        $user->calorieTrackings()->create([
            'tracked_date' => today(),
            'calories' => $eaten,
            'meal_name' => 'Tracked',
            'meal_type' => 'snack',
        ]);
    }

    return [$user, $mealPlan];
}

function addTool(User $user, array $cards = [['recipe_id' => 1, 'kcal' => 200]]): AddMealTool
{
    $alternatives = Mockery::mock(MealAlternatives::class);
    $alternatives->shouldReceive('suggest')->andReturn($cards);

    return new AddMealTool($user, $alternatives);
}

test('with no active plan it returns no_active_plan', function () {
    $user = User::factory()->withProfile()->create();

    $result = json_decode(addTool($user)->handle(new Request(['type' => 'snack'])), true);

    expect($result['error'])->toBe('no_active_plan');
});

test('adding a main slot that already exists offers a swap instead', function () {
    [$user] = addMealFixture(existingMealTypes: ['lunch']);

    $result = json_decode(addTool($user)->handle(new Request(['type' => 'lunch'])), true);

    expect($result['error'])->toBe('slot_already_planned');
});

test('a named add over the goal asks to confirm first', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1900);

    $result = json_decode(addTool($user)->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 300,
    ])), true);

    expect($result['error'])->toBe('budget_exceeded');
    expect($result['remaining'])->toBe(100);
});

test('filling with barely any room left returns no_room', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1950);

    $result = json_decode(addTool($user)->handle(new Request(['fill_remaining' => true])), true);

    expect($result['error'])->toBe('no_room');
});

test('proposes add cards for a snack within budget without creating a meal', function () {
    [$user, $mealPlan] = addMealFixture(goal: 2000, eaten: 1000);

    $result = json_decode(addTool($user)->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 250,
    ])), true);

    expect($result['widget'])->toBe('meal_alternatives');
    expect($result['data']['action'])->toBe('add');
    expect($result['data']['cards'])->toHaveCount(1);
    expect($mealPlan->meals()->count())->toBe(0);
});

test('filling open calories proposes add cards', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1500);

    $result = json_decode(addTool($user)->handle(new Request(['fill_remaining' => true])), true);

    expect($result['data']['action'])->toBe('add');
});

test('a confirmed over-goal add proposes cards', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1900);

    $result = json_decode(addTool($user)->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 300,
        'confirmed' => true,
    ])), true);

    expect($result['data']['action'])->toBe('add');
});

test('no matching candidates returns no_alternatives', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1000);

    $result = json_decode(addTool($user, [])->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 250,
    ])), true);

    expect($result['error'])->toBe('no_alternatives');
});
