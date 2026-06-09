<?php

use App\Enums\ActivityLevel;
use App\Enums\Gender;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function mobilePayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'mobile-test@example.com',
        'name' => 'Mobile User',
        'birthdate' => '1998-01-15',
        'gender' => Gender::MALE->value,
        'weight' => 80.0,
        'height' => 180,
        'body_goal' => 'build_muscle',
        'skill_level' => 'intermediate',
        'activity_level' => ActivityLevel::MAINLY_SITTING->value,
        'training_place' => 'gym',
        'dietary_preference' => 'omnivore',
        'training_sessions' => 4,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

test('v3 onboarding creates user with trial and mobile source', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload())
        ->assertCreated();

    $user = User::where('email', 'mobile-test@example.com')->first();

    expect($user->source->value)->toBe('mobile_apple')
        ->and($user->trial_ends_at)->not->toBeNull()
        ->and($user->isOnFreeTrial())->toBeTrue();
});

test('v3 onboarding stores meal preferences on profile', function () {
    Notification::fake();

    $recipes = Recipe::factory()->count(2)->create();

    postJson('/api/v3/onboarding', mobilePayload([
        'selected_meals' => ['breakfast', 'lunch', 'dinner'],
        'dislikes' => ['pork', 'mushrooms'],
        'cooking_time' => 'quick',
        'meal_variety' => 'low',
        'meal_prep_enabled' => true,
        'favorite_recipes' => $recipes->pluck('id')->toArray(),
    ]))->assertCreated();

    $user = User::where('email', 'mobile-test@example.com')->first();
    $profile = $user->profile;

    expect($profile->selected_meals)->toBe(['breakfast', 'lunch', 'dinner'])
        ->and($profile->food_dislikes)->toBe(['pork', 'mushrooms'])
        ->and($profile->cooking_preference->value)->toBe('quick')
        ->and($profile->meal_variety->value)->toBe('low')
        ->and($profile->meal_prep_enabled)->toBeTrue()
        ->and($user->favoriteRecipes)->toHaveCount(2);
});

test('v3 onboarding stores physical limitations', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload([
        'has_limitations' => true,
        'limitations' => ['knee', 'back'],
        'limitations_note' => 'Knee surgery 3 months ago',
    ]))->assertCreated();

    $profile = User::where('email', 'mobile-test@example.com')->first()->profile;

    expect($profile->physical_limitations)->toBe(['knee', 'back'])
        ->and($profile->physical_limitations_note)->toBe('Knee surgery 3 months ago');
});

test('v3 onboarding uses defaults for optional fields', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload())
        ->assertCreated();

    $profile = User::where('email', 'mobile-test@example.com')->first()->profile;

    expect($profile->selected_meals)->toBeNull()
        ->and($profile->food_dislikes)->toBe([])
        ->and($profile->cooking_preference->value)->toBe('normal')
        ->and($profile->meal_variety->value)->toBe('medium')
        ->and($profile->meal_prep_enabled)->toBeFalse()
        ->and($profile->physical_limitations)->toBe([]);
});

test('v3 onboarding creates plan with trial duration', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload())
        ->assertCreated();

    assertDatabaseHas('plans', [
        'duration_days' => (int) config('subscription.trial_days'),
    ]);
});

test('v3 onboarding dispatches generation jobs', function () {
    Bus::fake([GenerateUserWorkoutPlan::class, GenerateUserMealPlan::class]);
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload())
        ->assertCreated();

    Bus::assertDispatched(GenerateUserWorkoutPlan::class, 2);
    Bus::assertDispatched(GenerateUserMealPlan::class, 2);
});

test('v3 onboarding rejects old body_goal values', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload([
        'body_goal' => 'muscle_gain',
    ]))->assertUnprocessable()
        ->assertJsonValidationErrors('body_goal');
});

test('v3 onboarding validates cooking_time enum', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload([
        'cooking_time' => 'invalid',
    ]))->assertUnprocessable()
        ->assertJsonValidationErrors('cooking_time');
});

test('v3 onboarding validates meal_variety enum', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload([
        'meal_variety' => 'invalid',
    ]))->assertUnprocessable()
        ->assertJsonValidationErrors('meal_variety');
});

test('v3 onboarding accepts device_name without error', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload([
        'device_name' => 'iPhone 16 (iOS)',
    ]))->assertCreated();
});

test('v3 onboarding calculates age from birthdate', function () {
    Notification::fake();

    postJson('/api/v3/onboarding', mobilePayload([
        'birthdate' => '1995-03-15',
    ]))->assertCreated();

    $profile = User::where('email', 'mobile-test@example.com')->first()->profile;

    expect($profile->age)->toBeGreaterThanOrEqual(30)
        ->and($profile->age)->toBeLessThanOrEqual(32);
});
