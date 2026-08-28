<?php

use App\Models\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves the branded flex placeholder for a recipe-less flex meal', function () {
    config(['services.r2.public_url' => 'https://cdn.test']);

    $meal = Meal::factory()->create([
        'type' => 'flex',
        'recipe_id' => null,
        'image_full' => null,
        'image_isolated' => null,
    ]);

    expect($meal->thumbnail_url)->toBe('https://cdn.test/meals/thumbnails/flex_placeholder.png');
});
