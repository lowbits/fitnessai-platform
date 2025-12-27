<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;


test('password can be set with valid token', function () {
    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->postJson(route('set-password'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertStatus(200);

    // Verify password was updated
    $this->assertTrue(
        \Hash::check('NewPassword123!', $user->fresh()->password)
    );
});

test('password cannot be set with invalid token', function () {
    $user = User::factory()->create();

    $response = $this->postJson(route('set-password'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertStatus(422);
});

test('password cannot be set without matching confirmation', function () {
    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->postJson(route('set-password'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);

    $response->assertStatus(422);
});
