<?php

use App\Ai\Agents\MealPhotoAgent;
use App\Ai\Agents\NutritionLabelAgent;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('it reads a nutrition label into per-100 macros', function () {
    NutritionLabelAgent::fake([[
        'kcal' => 20,
        'protein_g' => 0,
        'carbs_g' => 5,
        'fat_g' => 0,
        'serving_size' => 500,
        'serving_unit' => 'ml',
    ]]);

    $this->actingAs(User::factory()->create())
        ->postJson('/api/v3/nutrition-label', ['image' => UploadedFile::fake()->image('label.jpg')])
        ->assertOk()
        ->assertJsonPath('data.kcal', 20)
        ->assertJsonPath('data.carbs_g', 5)
        ->assertJsonPath('data.serving_unit', 'ml');
});

test('it detects foods and portions in a meal photo', function () {
    MealPhotoAgent::fake([[
        'items' => [
            ['name' => 'Rice', 'portion_g' => 200, 'kcal' => 260, 'protein_g' => 5, 'carbs_g' => 56, 'fat_g' => 1, 'confidence' => 0.8],
            ['name' => 'Chicken', 'portion_g' => 150, 'kcal' => 250, 'protein_g' => 46, 'carbs_g' => 0, 'fat_g' => 6, 'confidence' => 0.9],
        ],
    ]]);

    $this->actingAs(User::factory()->create())
        ->postJson('/api/v3/meal-photos', ['image' => UploadedFile::fake()->image('meal.jpg')])
        ->assertOk()
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.items.0.name', 'Rice')
        ->assertJsonPath('data.items.1.kcal', 250);
});

test('vision endpoints require an image', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v3/meal-photos', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['image']);
});

test('vision endpoints require authentication', function () {
    $this->postJson('/api/v3/nutrition-label', ['image' => UploadedFile::fake()->image('label.jpg')])
        ->assertUnauthorized();
});
