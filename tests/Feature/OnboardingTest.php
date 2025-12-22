<?php

use App\Enums\ActivityLevel;
use App\Enums\Gender;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('allows user to complete onboarding without password', function () {
    Notification::fake();

    $gender = Gender::MALE->value;

    $response = post('/api/v2/onboarding', [
        'email' => $email = "test-{$gender}@example.com",
        'name' => 'Test User',
        'age' => 28,
        'gender' => $gender,
        'weight' => $weight = 80.0,
        'height' => $height = 180,
        'body_goal' => 'maintenance',
        'skill_level' => 'intermediate',
        'activity_level' => ActivityLevel::MAINLY_SITTING->value,
        'training_place' => 'gym',
        'diet_type' => 'omnivore',
        'training_sessions' => 4,
    ])->assertCreated();

    assertDatabaseHas('users', [
        'email' => $email,
        'email_verified_at' => null, // Not yet verified
    ]);

    assertDatabaseHas('user_profiles', [
        'weight_kg' => $weight,
        'height_cm' => $height,
    ]);

    assertDatabaseHas('plans', [
        'daily_calories' => 2148,
        'daily_protein_g' => 161,
        'daily_carbs_g' => 242,
        'daily_fat_g' => 60
    ]);

    // Verify notification was sent
    Notification::assertSentTo(
        \App\Models\User::where('email', $email)->first(),
        OnboardingCompleteVerifyEmail::class
    );

    // Verify response includes next_step
    $response->assertJson([
        'user' => [
            'email_verified' => false,
        ],
    ]);
});
