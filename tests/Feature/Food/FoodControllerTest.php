<?php

use App\Models\Food;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('it resolves a barcode to nutrition for an authenticated user', function () {
    Http::fake(['*' => Http::response([
        'status' => 1,
        'product' => [
            'code' => '5449000000996',
            'product_name' => 'Coca-Cola',
            'nutriments' => ['energy-kcal_100g' => 42],
        ],
    ])]);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v3/foods/5449000000996')
        ->assertOk()
        ->assertJsonPath('data.name', 'Coca-Cola')
        ->assertJsonPath('data.barcode', '5449000000996');

    expect(Food::where('barcode', '5449000000996')->exists())->toBeTrue();
});

test('it returns 404 when nutrition is unavailable', function () {
    Http::fake(['*' => Http::response(['status' => 0])]);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v3/foods/000')
        ->assertNotFound()
        ->assertJsonPath('error', 'not_found');
});

test('it requires authentication', function () {
    $this->getJson('/api/v3/foods/5449000000996')->assertUnauthorized();
});
