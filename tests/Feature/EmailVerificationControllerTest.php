<?php

use App\Enums\BodyGoal;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    Event::fake();
});

function createVerificationUrl(User $user, Plan $plan): string
{
    return URL::temporarySignedRoute('verification.verify-onboarding', now()->addHour(), [
        'locale' => 'en',
        'id' => $user->id,
        'hash' => sha1($user->getEmailForVerification()),
        'plan_id' => $plan->id,
    ]);
}

it('renders page with bodyGoal prop from user profile', function () {
    $user = User::factory()->unverified()->withProfile([
        'body_goal' => BodyGoal::MUSCLE_GAIN,
    ])->create();

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $url = createVerificationUrl($user, $plan);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('EmailVerification/GeneratingPlan')
            ->where('bodyGoal', 'muscle_gain')
        );
});

it('renders page with smartLink as signed download-app URL', function () {
    $user = User::factory()->unverified()->create();
    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $url = createVerificationUrl($user, $plan);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('EmailVerification/GeneratingPlan')
            ->where('smartLink', fn ($value) => str_contains($value, '/app')
                && str_contains($value, 'signature=')
                && str_contains($value, 'utm_source=web')
                && str_contains($value, 'utm_medium=generating_plan')
                && str_contains($value, 'utm_campaign=plan_generation')
            )
        );
});

it('bodyGoal is null when user has no profile', function () {
    $user = User::factory()->unverified()->create();
    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $url = createVerificationUrl($user, $plan);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('EmailVerification/GeneratingPlan')
            ->where('bodyGoal', null)
        );
});

it('status polling returns correct is_complete value when generating', function () {
    $user = User::factory()->unverified()->create();
    $plan = Plan::factory()->active()->create([
        'user_id' => $user->id,
        'generation_completed_at' => null,
    ]);

    $url = createVerificationUrl($user, $plan);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('EmailVerification/GeneratingPlan')
            ->where('status.is_complete', false)
            ->where('status.status', 'generating')
        );
});

it('status polling returns correct is_complete value when completed', function () {
    $user = User::factory()->unverified()->create();
    $plan = Plan::factory()->active()->create([
        'user_id' => $user->id,
        'generation_completed_at' => now(),
    ]);

    $url = createVerificationUrl($user, $plan);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('EmailVerification/GeneratingPlan')
            ->where('status.is_complete', true)
            ->where('status.status', 'completed')
        );
});
