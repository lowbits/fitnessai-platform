<?php

use App\Ai\Tools\LogMealTool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;

uses(RefreshDatabase::class);

test('it logs analyzed meal items into calorie trackings for today', function () {
    $user = User::factory()->create();

    $result = json_decode((new LogMealTool($user))->handle(new Request([
        'meal_type' => 'lunch',
        'items' => [
            ['name' => 'Reis', 'calories' => 260, 'protein_g' => 5, 'carbs_g' => 56, 'fat_g' => 1],
            ['name' => 'Hähnchen', 'calories' => 250, 'protein_g' => 46, 'carbs_g' => 0, 'fat_g' => 6],
        ],
    ])), true);

    expect($result['logged'])->toBeTrue()
        ->and($result['item_count'])->toBe(2)
        ->and($result['total_kcal'])->toBe(510);

    expect($user->calorieTrackings()->whereDate('tracked_date', today())->count())->toBe(2);
    expect((float) $user->calorieTrackings()->whereDate('tracked_date', today())->sum('calories'))->toBe(510.0);
    expect($user->calorieTrackings()->where('meal_name', 'Reis')->value('protein_g'))->toEqual(5.0);
});

test('it returns an error when there are no items', function () {
    $user = User::factory()->create();

    $result = json_decode((new LogMealTool($user))->handle(new Request(['items' => []])), true);

    expect($result)->toHaveKey('error')
        ->and($result['error'])->toBe('no_items');
    expect($user->calorieTrackings()->count())->toBe(0);
});
