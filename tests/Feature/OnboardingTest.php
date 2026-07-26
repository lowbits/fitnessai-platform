<?php

use App\Enums\ActivityLevel;
use App\Enums\Gender;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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
        'body_goal' => 'get_fit',
        'skill_level' => 'intermediate',
        'activity_level' => ActivityLevel::MAINLY_SITTING->value,
        'training_place' => 'gym',
        'dietary_preference' => 'omnivore',
        'training_sessions' => 4,
    ])->assertCreated();

    assertDatabaseHas('users', [
        'email' => $email,
        'email_verified_at' => null, // Not yet verified
    ]);

    $user = User::where('email', $email)->first();
    expect($user->trial_ends_at)->toBeNull();
    expect($user->isOnFreeTrial())->toBeFalse();

    assertDatabaseHas('user_profiles', [
        'weight_kg' => $weight,
        'height_cm' => $height,
    ]);

    assertDatabaseHas('plans', [
        'daily_calories' => 2377,
        'daily_protein_g' => 144,
        'daily_carbs_g' => 270,
        'daily_fat_g' => 80,
    ]);

    // Verify notification was sent
    Notification::assertSentTo(
        User::where('email', $email)->first(),
        OnboardingCompleteVerifyEmail::class
    );

    // Verify response includes next_step
    $response->assertJson([
        'user' => [
            'email_verified' => false,
        ],
    ]);
});

test('mobile onboarding dispatches day 1 generation jobs immediately', function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);
    Notification::fake();

    post('/api/v2/onboarding', [
        'email' => 'gen-test@example.com',
        'name' => 'Test User',
        'age' => 25,
        'gender' => Gender::MALE->value,
        'weight' => 75.0,
        'height' => 175,
        'body_goal' => 'get_fit',
        'skill_level' => 'beginner',
        'activity_level' => ActivityLevel::MAINLY_SITTING->value,
        'training_place' => 'gym',
        'dietary_preference' => 'omnivore',
        'training_sessions' => 3,
        'source' => 'mobile_apple',
    ])->assertCreated();

    Bus::assertDispatched(GenerateUserWorkoutPlan::class, 2);
    Bus::assertDispatched(GenerateUserMealPlan::class, 2);
});

test('web onboarding does not dispatch generation jobs immediately', function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);
    Notification::fake();

    post('/api/v2/onboarding', [
        'email' => 'web-test@example.com',
        'name' => 'Web User',
        'age' => 25,
        'gender' => Gender::MALE->value,
        'weight' => 75.0,
        'height' => 175,
        'body_goal' => 'get_fit',
        'skill_level' => 'beginner',
        'activity_level' => ActivityLevel::MAINLY_SITTING->value,
        'training_place' => 'gym',
        'dietary_preference' => 'omnivore',
        'training_sessions' => 3,
    ])->assertCreated();

    Bus::assertNotDispatched(GenerateUserWorkoutPlan::class);
    Bus::assertNotDispatched(GenerateUserMealPlan::class);
});
