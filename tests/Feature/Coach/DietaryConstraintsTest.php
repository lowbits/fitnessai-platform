<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Ai\Support\DietaryConstraints;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it reflects the diet preference and disliked ingredients', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'dietary_preference' => 'vegetarian',
        'food_dislikes' => ['pilze', 'koriander'],
    ]);

    $text = DietaryConstraints::forProfile($user->profile->fresh());

    expect($text)->toContain('vegetarian')
        ->toContain('pilze')
        ->toContain('koriander');
});

test('it is empty for an omnivore with no dislikes', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'dietary_preference' => 'omnivore',
        'diet_style' => null,
        'food_dislikes' => [],
    ]);

    expect(DietaryConstraints::forProfile($user->profile->fresh()))->toBe('');
});

test('mona instructions carry the dietary constraints', function () {
    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'dietary_preference' => 'vegan',
        'food_dislikes' => ['sellerie'],
    ]);

    $instructions = (new MonaCoachAgent($user->fresh()))->instructions();

    expect($instructions)->toContain('vegan')->toContain('sellerie');
});
