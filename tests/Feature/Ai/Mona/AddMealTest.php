<?php

use App\Ai\Support\MealDrafter;
use App\Ai\Tools\AddMealTool;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\Recipe;
use App\Models\User;
use App\Services\Recipe\MealAlternatives;
use Laravel\Ai\Tools\Request;

function alternativesStub(): MealAlternatives
{
    $stub = Mockery::mock(MealAlternatives::class);
    $stub->shouldReceive('for')->andReturn([
        'meal_id' => 1,
        'slot' => 'snack',
        'original' => [],
        'cards' => [['recipe_id' => 1]],
    ]);

    return $stub;
}

/**
 * Mona playbook — add a meal to today. The LLM drafting is faked so these
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

function fakeDrafter(?Recipe $recipe): MealDrafter
{
    return new class($recipe) extends MealDrafter
    {
        public function __construct(private ?Recipe $recipe) {}

        public function draft(User $user, string $mealType, int $targetKcal, ?string $request = null): ?Recipe
        {
            return $this->recipe;
        }
    };
}

function neverDrafter(): MealDrafter
{
    return new class extends MealDrafter
    {
        public function draft(User $user, string $mealType, int $targetKcal, ?string $request = null): ?Recipe
        {
            throw new RuntimeException('drafter should not run — a guard should have returned first');
        }
    };
}

test('with no active plan it returns no_active_plan', function () {
    $user = User::factory()->withProfile()->create();

    $result = json_decode((new AddMealTool($user, neverDrafter(), alternativesStub()))->handle(new Request(['type' => 'snack'])), true);

    expect($result['error'])->toBe('no_active_plan');
});

test('adding a main slot that already exists offers a swap instead', function () {
    [$user] = addMealFixture(existingMealTypes: ['lunch']);

    $result = json_decode((new AddMealTool($user, neverDrafter(), alternativesStub()))->handle(new Request(['type' => 'lunch'])), true);

    expect($result['error'])->toBe('slot_already_planned');
});

test('a named add over the goal asks to confirm first', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1900);

    $result = json_decode((new AddMealTool($user, neverDrafter(), alternativesStub()))->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 300,
    ])), true);

    expect($result['error'])->toBe('budget_exceeded');
    expect($result['remaining'])->toBe(100);
});

test('a confirmed over-goal add creates the meal', function () {
    [$user, $mealPlan] = addMealFixture(goal: 2000, eaten: 1900);
    $recipe = Recipe::factory()->create(['calories' => 300]);

    $result = json_decode((new AddMealTool($user, fakeDrafter($recipe), alternativesStub()))->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 300,
        'confirmed' => true,
    ])), true);

    expect($result['widget'])->toBe('meal_alternatives');
    expect($mealPlan->meals()->where('type', 'snack')->count())->toBe(1);
});

test('filling with barely any room left returns no_room', function () {
    [$user] = addMealFixture(goal: 2000, eaten: 1950);

    $result = json_decode((new AddMealTool($user, neverDrafter(), alternativesStub()))->handle(new Request([
        'fill_remaining' => true,
    ])), true);

    expect($result['error'])->toBe('no_room');
});

test('filling open calories drafts and adds a snack', function () {
    [$user, $mealPlan] = addMealFixture(goal: 2000, eaten: 1500);
    $recipe = Recipe::factory()->create(['calories' => 500]);

    $result = json_decode((new AddMealTool($user, fakeDrafter($recipe), alternativesStub()))->handle(new Request([
        'fill_remaining' => true,
    ])), true);

    expect($result['widget'])->toBe('meal_alternatives');
    expect($mealPlan->meals()->where('type', 'snack')->count())->toBe(1);
});

test('a snack within budget is added', function () {
    [$user, $mealPlan] = addMealFixture(goal: 2000, eaten: 1000);
    $recipe = Recipe::factory()->create(['calories' => 250]);

    $result = json_decode((new AddMealTool($user, fakeDrafter($recipe), alternativesStub()))->handle(new Request([
        'type' => 'snack',
        'approx_kcal' => 250,
    ])), true);

    expect($result['widget'])->toBe('meal_alternatives');
    expect($mealPlan->meals()->where('type', 'snack')->count())->toBe(1);
});
