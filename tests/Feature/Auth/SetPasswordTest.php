<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;


test('password can be set with valid token', function () {
    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->postJson(route('api.set-password'), [
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

    $response = $this->postJson(route('api.set-password'), [
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

    $response = $this->postJson(route('api.set-password'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);

    $response->assertStatus(422);
});

test('set password view can be accessed with valid signed url', function () {
    $token = 'test-token-123';
    $email = 'test@example.com';

    $signedUrl = URL::temporarySignedRoute(
        'set-password',
        now()->addHours(24),
        [
            'locale' => 'en',
            'token' => $token,
            'email' => $email,
        ]
    );

    $response = $this->get($signedUrl);

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('SetPassword')
        ->has('token')
        ->has('email')
        ->where('token', $token)
        ->where('email', $email)
    );
});

test('set password view cannot be accessed with invalid signature', function () {
    $response = $this->get('/en/set-password/test-token-123?email=test@example.com');

    $response->assertStatus(403); // Forbidden - invalid signature
});

test('set password view cannot be accessed with expired signature', function () {
    $token = 'test-token-123';
    $email = 'test@example.com';

    // Create URL that expired 1 hour ago
    $expiredUrl = URL::temporarySignedRoute(
        'set-password',
        now()->subHour(),
        [
            'locale' => 'en',
            'token' => $token,
            'email' => $email,
        ]
    );

    $response = $this->get($expiredUrl);

    $response->assertStatus(403); // Forbidden - signature expired
});

test('set password view cannot be accessed with tampered token', function () {
    $token = 'test-token-123';
    $email = 'test@example.com';

    $signedUrl = URL::temporarySignedRoute(
        'set-password',
        now()->addHours(24),
        [
            'locale' => 'en',
            'token' => $token,
            'email' => $email,
        ]
    );

    // Tamper with the token parameter
    $tamperedUrl = str_replace($token, 'hacked-token', $signedUrl);

    $response = $this->get($tamperedUrl);

    $response->assertStatus(403); // Forbidden - signature invalid due to tampering
});

test('set password view uses correct locale in url', function () {
    $token = 'test-token-123';
    $email = 'test@example.com';

    // Test German locale
    $signedUrlDe = URL::temporarySignedRoute(
        'set-password',
        now()->addHours(24),
        [
            'locale' => 'de',
            'token' => $token,
            'email' => $email,
        ]
    );

    $response = $this->get($signedUrlDe);

    $response->assertStatus(200);
    expect($signedUrlDe)->toContain('/de/set-password/');
});
