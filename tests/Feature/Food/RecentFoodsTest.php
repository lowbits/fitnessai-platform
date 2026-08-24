<?php

use App\Enums\MealType;
use App\Models\CalorieTracking;
use App\Models\User;

test('it returns recent foods for a meal, deduped and most recent first', function () {
    $user = User::factory()->create();

    CalorieTracking::factory()->for($user)->create(['meal_type' => MealType::BREAKFAST, 'meal_name' => 'Oats', 'external_id' => 'oat1']);
    CalorieTracking::factory()->for($user)->create(['meal_type' => MealType::BREAKFAST, 'meal_name' => 'Oats', 'external_id' => 'oat1']);
    CalorieTracking::factory()->for($user)->create(['meal_type' => MealType::BREAKFAST, 'meal_name' => 'Eggs', 'external_id' => 'egg1']);
    CalorieTracking::factory()->for($user)->create(['meal_type' => MealType::LUNCH, 'meal_name' => 'Salad', 'external_id' => 'sal1']);

    $data = $this->actingAs($user)->getJson('/api/v3/foods/recent?meal=breakfast')->assertOk()->json('data');
    $names = collect($data)->pluck('name');

    expect($names)->toContain('Oats')->toContain('Eggs')->not->toContain('Salad');
    expect($names->filter(fn ($n) => $n === 'Oats'))->toHaveCount(1);
    expect($names->values()->all())->toBe(['Eggs', 'Oats']);
    expect($data[0])->toHaveKey('kcal')->not->toHaveKey('calories');
});

test('it rejects an invalid meal', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v3/foods/recent?meal=brunch')
        ->assertUnprocessable();
});

test('recent foods require authentication', function () {
    $this->getJson('/api/v3/foods/recent')->assertUnauthorized();
});
