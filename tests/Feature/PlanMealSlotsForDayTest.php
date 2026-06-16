<?php

use App\Actions\PlanMealSlotsForDay;
use App\Enums\MealVariety;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makePlanWithProfile(MealVariety $variety = MealVariety::MEDIUM): array
{
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create([
        'user_id' => $user->id,
        'meal_variety' => $variety,
        'selected_meals' => ['breakfast', 'lunch', 'snack', 'dinner'],
    ]);
    $plan = Plan::factory()->create(['user_id' => $user->id, 'duration_days' => 28, 'start_date' => now()]);

    return [$plan, $profile];
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
        ->toBe(['breakfast' => 2, 'lunch' => 2, 'snack' => 1, 'dinner' => 2]);

    expect(MealVariety::MEDIUM->perSlotDistinctTargets())
        ->toBe(['breakfast' => 4, 'lunch' => 5, 'snack' => 3, 'dinner' => 5]);

    expect(MealVariety::HIGH->perSlotDistinctTargets())
        ->toBe(['breakfast' => 7, 'lunch' => 7, 'snack' => 5, 'dinner' => 7]);
});

test('day 1 returns NEW for every slot with empty forbidden lists', function () {
    [$plan, $profile] = makePlanWithProfile();

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 1, profile: $profile);

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

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 3, profile: $profile);

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

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 3, profile: $profile);

    expect($result['lunch']['action'])->toBe('repeat');
    expect($result['lunch']['repeat_from']->name)
        ->toBeIn(['Carbonara', 'Tuna Bowl']);
});

test('repeat candidate picks least-used meal first', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Carbonara used twice, Tuna Bowl once → next repeat should pick Tuna Bowl.
    seedDay($plan, 1, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 2, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 3, [['type' => 'lunch', 'name' => 'Tuna Bowl']]);

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 4, profile: $profile);

    expect($result['lunch']['action'])->toBe('repeat');
    expect($result['lunch']['repeat_from']->name)->toBe('Tuna Bowl');
});

test('variety budget resets at the start of week 2', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Week 1: 2 distinct lunches → budget exhausted within week 1.
    seedDay($plan, 1, [['type' => 'lunch', 'name' => 'Carbonara']]);
    seedDay($plan, 7, [['type' => 'lunch', 'name' => 'Tuna Bowl']]);

    // Day 8 = start of week 2 → no prior meals in this week → NEW with empty forbidden.
    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 8, profile: $profile);

    expect($result['lunch']['action'])->toBe('new');
    expect($result['lunch']['forbidden_meals'])->toBeEmpty();
});

test('respects user selected_meals (no snack slot when user did not pick it)', function () {
    [$plan, $profile] = makePlanWithProfile();
    $profile->update(['selected_meals' => ['breakfast', 'lunch', 'dinner']]);

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 1, profile: $profile);

    expect(array_keys($result))->toEqualCanonicalizing(['breakfast', 'lunch', 'dinner']);
});

test('LOW tier exhausts dinner budget by day 3', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::LOW);

    // Low dinner target = 2.
    seedDay($plan, 1, [['type' => 'dinner', 'name' => 'Salmon Bowl']]);
    seedDay($plan, 2, [['type' => 'dinner', 'name' => 'Chicken Curry']]);

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 3, profile: $profile);

    expect($result['dinner']['action'])->toBe('repeat');
});

test('HIGH tier never returns REPEAT within first 7 days', function () {
    [$plan, $profile] = makePlanWithProfile(MealVariety::HIGH);

    // High dinner target = 7. Seed 6 distinct dinners; day 7 still NEW.
    foreach (range(1, 6) as $day) {
        seedDay($plan, $day, [['type' => 'dinner', 'name' => "Dinner Day {$day}"]]);
    }

    $result = (new PlanMealSlotsForDay)->handle($plan, dayNumber: 7, profile: $profile);

    expect($result['dinner']['action'])->toBe('new');
    expect($result['dinner']['forbidden_meals'])->toHaveCount(6);
});
