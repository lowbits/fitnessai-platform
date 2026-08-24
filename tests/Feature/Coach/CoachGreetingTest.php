<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('it welcomes a brand-new user', function () {
    Sanctum::actingAs(User::factory()->create(['name' => 'Lisa', 'locale' => 'en']));

    getJson('/api/v3/coach/greeting')
        ->assertOk()
        ->assertJsonPath('message', "Hey Lisa! I'm Mona, your coach. Where should we start?");
});

test('it celebrates weight loss with the numbers', function () {
    $user = User::factory()->withProfile()->create(['name' => 'Max Mustermann', 'locale' => 'en']);
    $user->profile->update(['weight_kg' => 90]);
    $user->bodyProgress()->create(['weight_kg' => 87, 'recorded_at' => now()]);

    Sanctum::actingAs($user);

    getJson('/api/v3/coach/greeting')
        ->assertOk()
        ->assertJsonPath('message', 'Nice work Max — 3 kg down since you started. How is your week going?');
});

test('it replies in the user locale', function () {
    Sanctum::actingAs(User::factory()->create(['name' => 'Lisa', 'locale' => 'de']));

    getJson('/api/v3/coach/greeting')
        ->assertOk()
        ->assertJsonPath('message', 'Hey Lisa! Ich bin Mona, dein Coach. Womit legen wir los?');
});
