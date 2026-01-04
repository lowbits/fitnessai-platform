<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('meal can be marked as completed', function () {
    $meal = Meal::factory()->create([
        'completed_at' => null,
    ]);

    expect($meal->isCompleted())->toBe(false);

    $meal->markAsCompleted();

    expect($meal->isCompleted())->toBe(true);
    expect($meal->completed_at)->not->toBeNull();
});

test('meal can be marked as incomplete', function () {
    $meal = Meal::factory()->create([
        'completed_at' => now(),
    ]);

    expect($meal->isCompleted())->toBe(true);

    $meal->markAsIncomplete();

    expect($meal->isCompleted())->toBe(false);
    expect($meal->completed_at)->toBeNull();
});

