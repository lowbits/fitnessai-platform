<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

it('toggles auto_fill_calories on the profile', function () {
    $user = User::factory()->withProfile(['auto_fill_calories' => true])->create();
    Sanctum::actingAs($user);

    patchJson('/api/v3/profile', ['auto_fill_calories' => false])
        ->assertOk()
        ->assertJsonPath('user.profile.auto_fill_calories', false);

    expect($user->profile->fresh()->auto_fill_calories)->toBeFalse();
});

it('rejects a non-boolean auto_fill_calories', function () {
    Sanctum::actingAs(User::factory()->withProfile()->create());

    patchJson('/api/v3/profile', ['auto_fill_calories' => 'nope'])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('auto_fill_calories');
});
