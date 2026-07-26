<?php

use App\Enums\ActivityLevel;
use App\Enums\Gender;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);
    Notification::fake();
});

function onboardingProfile(array $overrides = []): array
{
    return array_merge([
        'birthdate' => '1998-01-15',
        'gender' => Gender::MALE->value,
        'weight_kg' => 80.0,
        'height_cm' => 180,
        'body_goal' => 'build_muscle',
        'skill_level' => 'intermediate',
        'activity_level' => ActivityLevel::MAINLY_SITTING->value,
        'training_place' => 'gym',
        'dietary_preference' => 'omnivore',
        'training_sessions_per_week' => 4,
    ], $overrides);
}

function mockSocialite(string $provider, array $attrs): void
{
    $socialUser = Mockery::mock(SocialiteUser::class);
    $socialUser->shouldReceive('getId')->andReturn($attrs['id']);
    $socialUser->shouldReceive('getEmail')->andReturn($attrs['email'] ?? null);
    $socialUser->shouldReceive('getName')->andReturn($attrs['name'] ?? null);
    $socialUser->shouldReceive('getNickname')->andReturn(null);
    $socialUser->shouldReceive('getAvatar')->andReturn($attrs['avatar'] ?? null);

    $driver = Mockery::mock();
    $driver->shouldReceive('stateless')->andReturnSelf();
    $driver->shouldReceive('userFromToken')->andReturn($socialUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);
}

test('signup with password creates user, profile, trial and returns a token', function () {
    postJson('/api/v3/auth/signup', onboardingProfile([
        'name' => 'Pass User',
        'email' => 'pass@example.com',
        'auth' => ['type' => 'password', 'password' => 'password123'],
        'device_name' => 'iPhone',
    ]))
        ->assertCreated()
        ->assertJsonPath('user.email', 'pass@example.com')
        ->assertJsonPath('user.email_verified', false)
        ->assertJsonStructure(['user' => ['id', 'profile'], 'api_token']);

    $user = User::where('email', 'pass@example.com')->first();

    expect($user->password)->not->toBeNull()
        ->and($user->provider)->toBeNull()
        ->and($user->trial_ends_at)->toBeNull()
        ->and($user->isOnFreeTrial())->toBeFalse()
        ->and($user->profile)->not->toBeNull();

    Bus::assertDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertDispatched(GenerateUserMealPlan::class);
});

test('signup with google creates a verified social user with no password', function () {
    mockSocialite('google', [
        'id' => 'g-123',
        'email' => 'social@example.com',
        'name' => 'Social User',
        'avatar' => 'https://img/a.png',
    ]);

    postJson('/api/v3/auth/signup', onboardingProfile([
        'auth' => ['type' => 'google', 'token' => 'valid-token'],
        'device_name' => 'Pixel',
    ]))
        ->assertCreated()
        ->assertJsonPath('user.email', 'social@example.com')
        ->assertJsonPath('user.email_verified', true)
        ->assertJsonStructure(['api_token']);

    $user = User::where('email', 'social@example.com')->first();

    expect($user->password)->toBeNull()
        ->and($user->provider)->toBe('google')
        ->and($user->provider_id)->toBe('g-123')
        ->and($user->avatar)->toBe('https://img/a.png')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->trial_ends_at)->toBeNull()
        ->and($user->profile)->not->toBeNull();
});

test('signup with apple works via the extended provider', function () {
    mockSocialite('apple', [
        'id' => 'a-999',
        'email' => 'apple@example.com',
        'name' => 'Apple User',
    ]);

    postJson('/api/v3/auth/signup', onboardingProfile([
        'auth' => ['type' => 'apple', 'token' => 'apple-jwt'],
    ]))->assertCreated();

    expect(User::where('provider', 'apple')->where('provider_id', 'a-999')->exists())->toBeTrue();
});

test('social signup with an already-registered email is rejected', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    mockSocialite('google', [
        'id' => 'g-collide',
        'email' => 'existing@example.com',
        'name' => 'Whoever',
    ]);

    postJson('/api/v3/auth/signup', onboardingProfile([
        'auth' => ['type' => 'google', 'token' => 'valid-token'],
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('auth');

    expect(User::count())->toBe(1);

    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
});

test('signup rejects an unverifiable social token', function () {
    $driver = Mockery::mock();
    $driver->shouldReceive('stateless')->andReturnSelf();
    $driver->shouldReceive('userFromToken')->andThrow(new RuntimeException('bad token'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($driver);

    postJson('/api/v3/auth/signup', onboardingProfile([
        'auth' => ['type' => 'google', 'token' => 'broken'],
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('auth.token');
});

test('login with password returns a token', function () {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password123'),
    ]);

    postJson('/api/v3/auth/login', [
        'email' => 'login@example.com',
        'auth' => ['type' => 'password', 'password' => 'password123'],
        'device_name' => 'iPhone',
    ])
        ->assertOk()
        ->assertJsonPath('user.email', 'login@example.com')
        ->assertJsonStructure(['api_token']);
});

test('login with wrong password fails', function () {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password123'),
    ]);

    postJson('/api/v3/auth/login', [
        'email' => 'login@example.com',
        'auth' => ['type' => 'password', 'password' => 'wrong'],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

test('login with a known social identity returns a token', function () {
    User::factory()->create([
        'email' => 'known@example.com',
        'provider' => 'google',
        'provider_id' => 'g-known',
    ]);

    mockSocialite('google', ['id' => 'g-known', 'email' => 'known@example.com']);

    postJson('/api/v3/auth/login', [
        'auth' => ['type' => 'google', 'token' => 'valid-token'],
    ])
        ->assertOk()
        ->assertJsonStructure(['api_token']);
});

test('login with an unknown social identity is rejected', function () {
    mockSocialite('google', ['id' => 'g-nobody', 'email' => 'nobody@example.com']);

    postJson('/api/v3/auth/login', [
        'auth' => ['type' => 'google', 'token' => 'valid-token'],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('auth');
});

test('social login links to an existing email account', function () {
    $existing = User::factory()->create(['email' => 'linkme@example.com']);

    mockSocialite('google', ['id' => 'g-link', 'email' => 'linkme@example.com']);

    postJson('/api/v3/auth/login', [
        'auth' => ['type' => 'google', 'token' => 'valid-token'],
    ])->assertOk();

    $existing->refresh();

    expect($existing->provider)->toBe('google')
        ->and($existing->provider_id)->toBe('g-link');
});
