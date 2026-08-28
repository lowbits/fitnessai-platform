<?php

use App\Actions\PlanMealSlotsForDay;
use App\Enums\MealVariety;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makePlanWithProfile(MealVariety $variety = MealVariety::MEDIUM): array
{
    $user = User::factory()
        ->withProfile([
            'meal_variety' => $variety,
            'selected_meals' => ['breakfast', 'lunch', 'snack', 'dinner'],
        ])
        ->create();

    $plan = Plan::factory()->create(['user_id' => $user->id, 'duration_days' => 28, 'start_date' => now()]);

    return [$plan, $user->profile];
}

function seedDay(Plan $plan, int $dayNumber, array $meals): MealPlan
{
    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => $dayNumber,
        'status' => 'generated',
    ]);

    foreach ($meals as $meal) {
        Meal::factory()->create(array_merge([
            'meal_plan_id' => $mealPlan->id,
        ], $meal));
    }

    return $mealPlan;
}

test('MealVariety per-slot distinct targets match spec', function () {
    expect(MealVariety::LOW->perSlotDistinctTargets())
        ->toBe(['breakfast' => 2, 'lunch' => 2, 'snack' => 2, 'dinner' => 2]);

    expect(MealVariety::MEDIUM->perSlotDistinctTargets())
        ->toBe(['breakfast' => 4, 'lunch' => 5, 'snack' => 5, 'dinner' => 5]);

    expect(MealVariety::HIGH->perSlotDistinctTargets())
        ->toBe(['breakfast' => 7, 'lunch' => 7, 'snack' => 7, 'dinner' => 7]);
});

test('day 1 returns NEW for every slot with empty forbidden lists', function () {
    [$plan, $profile] = makePlanWithProfile();

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 1, profile: $profile);

    expect($result)->toHaveKeys(['breakfast', 'lunch', 'snack', 'dinner']);
    foreach ($result as $slot => $entry) {
        expect($entry['action'])->toBe('new');
        expect($entry['forbidden_meals'])->toBeEmpty();
    }
});

test('slot below distinct budget returns NEW with prior meals as forbidden', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::MEDIUM);

    // Medium lunch target = 5. Seed 2 distinct lunches so far → still NEW.
    seedDay($plan, 1, [
        ['type' => 'lunch', 'name' => 'Carbonara', 'primary_protein' => 'pork', 'cuisine' => 'mediterranean'],
    ]);
    seedDay($plan, 2, [
        ['type' => 'lunch', 'name' => 'Tuna Bowl', 'primary_protein' => 'fish', 'cuisine' => 'asian'],
    ]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 3, profile: $profile);

    expect($result['lunch']['action'])->toBe('new');
    expect($result['lunch']['forbidden_meals']->pluck('name')->all())
        ->toEqualCanonicalizing(['Carbonara', 'Tuna Bowl']);
});

test('slot at distinct budget returns REPEAT of a prior meal', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Low lunch target = 2. Seed 2 distinct lunches → next must REPEAT.
    seedDay($plan, 1, [
        ['type' => 'lunch', 'name' => 'Carbonara', 'primary_protein' => 'pork', 'cuisine' => 'mediterranean'],
    ]);
    seedDay($plan, 2, [
        ['type' => 'lunch', 'name' => 'Tuna Bowl', 'primary_protein' => 'fish', 'cuisine' => 'asian'],
    ]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 3, profile: $profile);

    expect($result['lunch']['action'])->toBe('repeat');
    expect($result['lunch']['repeat_from']->name)
        ->toBeIn(['Carbonara', 'Tuna Bowl']);
});

test('repeat candidate picks least-used meal first (and never yesterday)', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Carbonara used twice (days 1, 3), Tuna Bowl once (day 2).
    // For day 4: yesterday was Carbonara (day 3) → excluded. Tuna Bowl wins on least-used too.
    seedDay($plan, 1, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 2, [['type' => 'lunch', 'name' => 'Tuna Bowl']]);
    seedDay($plan, 3, [['type' => 'lunch', 'name' => 'Carbonara']]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 4, profile: $profile);

    expect($result['lunch']['action'])->toBe('repeat');
    expect($result['lunch']['repeat_from']->name)->toBe('Tuna Bowl');
});

test('repeat candidate never picks yesterday even when it would be least-used', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Carbonara twice, Tuna Bowl yesterday. Without the rule, Tuna Bowl wins (least-used).
    // With the rule, Carbonara wins because Tuna Bowl was yesterday.
    seedDay($plan, 1, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 2, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 3, [['type' => 'lunch', 'name' => 'Tuna Bowl']]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 4, profile: $profile);

    expect($result['lunch']['action'])->toBe('repeat');
    expect($result['lunch']['repeat_from']->name)->toBe('Carbonara');
});

test('variety budget resets at the start of week 2', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Week 1: 2 distinct lunches → budget exhausted within week 1.
    seedDay($plan, 1, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 7, [['type' => 'lunch', 'name' => 'Tuna Bowl']]);

    // Day 8 = start of week 2 → no prior meals in this week → NEW with empty forbidden.
    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 8, profile: $profile);

    expect($result['lunch']['action'])->toBe('new');
    expect($result['lunch']['forbidden_meals'])->toBeEmpty();
});

test('respects user selected_meals (no snack slot when user did not pick it)', function () {
    [$plan, $profile] = makePlanWithProfile();
    $profile->update(['selected_meals' => ['breakfast', 'lunch', 'dinner'], 'auto_fill_calories' => false]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 1, profile: $profile);

    expect(array_keys($result))->toEqualCanonicalizing(['breakfast', 'lunch', 'dinner']);
});

test('adds a freshly generated flex slot on a high-calorie day when auto-fill is on', function () {
    [$plan, $profile] = makePlanWithProfile();
    $profile->update([
        'selected_meals' => ['breakfast', 'lunch', 'dinner'],
        'auto_fill_calories' => true,
        'gender' => 'male',
        'weight_kg' => 120,
        'height_cm' => 200,
        'birthdate' => now()->subYears(25)->format('Y-m-d'),
        'activity_level' => 'hard_working',
        'training_sessions_per_week' => 7,
    ]);
    // Deterministically high-calorie so 3 mains overflow the cap and force a flex slot.
    expect((int) $profile->getMetabolismData()['daily_calories'])->toBeGreaterThan(2700);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 1, profile: $profile);

    expect($result)->toHaveKey('flex')
        ->and($result['flex']['action'])->toBe('new');
});

test('LOW tier exhausts dinner budget by day 3', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Low dinner target = 2.
    seedDay($plan, 1, [['type' => 'dinner', 'name' => 'Salmon Bowl']]);
    seedDay($plan, 2, [['type' => 'dinner', 'name' => 'Chicken Curry']]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 3, profile: $profile);

    expect($result['dinner']['action'])->toBe('repeat');
});

test('forbidden_meals is cross-slot: a new lunch sees prior dinners and breakfasts too', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::HIGH);

    // Day 1: one of each slot — none should slip past the lunch on day 2.
    seedDay($plan, 1, [
        ['type' => 'breakfast', 'name' => 'Overnight Oats'],
        ['type' => 'lunch', 'name' => 'Carbonara'],
        ['type' => 'dinner', 'name' => 'Spinat-Ricotta-Lasagne'],
    ]);

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 2, profile: $profile);

    // The lunch slot's forbidden list must include the dinner (Lasagne) and
    // the breakfast (Overnight Oats) — otherwise template-twin Cannelloni
    // would slip through for lunch.
    $names = $result['lunch']['forbidden_meals']->pluck('name')->all();
    expect($names)->toContain('Overnight Oats', 'Carbonara', 'Spinat-Ricotta-Lasagne');
});

test('HIGH tier never returns REPEAT within first 7 days', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::HIGH);

    // High dinner target = 7. Seed 6 distinct dinners; day 7 still NEW.
    foreach (range(1, 6) as $day) {
        seedDay($plan, $day, [['type' => 'dinner', 'name' => "Dinner Day {$day}"]]);
    }

    $result = app(PlanMealSlotsForDay::class)->handle($plan, dayNumber: 7, profile: $profile);

    expect($result['dinner']['action'])->toBe('new')
        ->and($result['dinner']['forbidden_meals'])->toHaveCount(6);
});
