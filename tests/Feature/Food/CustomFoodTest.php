<?php

use App\Enums\FoodSource;
use App\Models\Food;
use App\Models\User;

test('a user creates a custom food', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v3/foods', [
            'name' => 'Omas Lasagne',
            'kcal' => 180,
            'protein_g' => 9,
            'carbs_g' => 12,
            'fat_g' => 10,
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Omas Lasagne')
        ->assertJsonPath('data.source', 'custom');

    $food = Food::sole();
    expect($food->source)->toBe(FoodSource::Custom);
    expect($food->user_id)->not->toBeNull();
    expect($food->kcal)->toBe(180.0);
});

test('a custom food requires a name and calories', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v3/foods', ['brand' => 'x'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'kcal']);
});

test('creating a custom food requires authentication', function () {
    $this->postJson('/api/v3/foods', ['name' => 'x', 'kcal' => 1])->assertUnauthorized();
});
