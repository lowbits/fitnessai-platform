<?php

use App\Enums\NewsletterStatus;
use App\Events\EmailVerified;
use App\Models\NewsletterSubscriber;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\NewsletterConfirmation;
use App\Services\NewsletterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

it('captures an android waitlist signup as pending and sends a confirmation email', function () {
    $this->postJson('/newsletter/subscribe', [
        'email' => 'android@example.com',
        'locale' => 'de',
        'consent' => true,
        'source' => 'android_waitlist',
    ])->assertStatus(202);

    $subscriber = NewsletterSubscriber::where('email', 'android@example.com')->first();

    expect($subscriber)->not->toBeNull()
        ->and($subscriber->status)->toBe(NewsletterStatus::Pending)
        ->and($subscriber->source)->toBe('android_waitlist')
        ->and($subscriber->consent_text)->toBe(trans('newsletter.consent', [], 'de'));

    Notification::assertSentTo($subscriber, NewsletterConfirmation::class);
});

it('rejects a subscribe without consent', function () {
    $this->postJson('/newsletter/subscribe', [
        'email' => 'noconsent@example.com',
        'consent' => false,
    ])->assertStatus(422)->assertJsonValidationErrors('consent');

    expect(NewsletterSubscriber::count())->toBe(0);
});

it('confirms a subscriber via a valid signed link', function () {
    $subscriber = NewsletterSubscriber::create([
        'email' => 'confirm@example.com',
        'locale' => 'en',
        'status' => NewsletterStatus::Pending,
    ]);

    $url = URL::temporarySignedRoute('newsletter.confirm', now()->addDays(7), [
        'subscriber' => $subscriber->id,
        'locale' => 'en',
    ]);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Newsletter/Confirmed')
            ->where('status', 'confirmed'));

    expect($subscriber->fresh()->status)->toBe(NewsletterStatus::Confirmed);
});

it('does not confirm on an invalid signature', function () {
    $subscriber = NewsletterSubscriber::create([
        'email' => 'tampered@example.com',
        'locale' => 'en',
        'status' => NewsletterStatus::Pending,
    ]);

    $this->get("/newsletter/confirm/{$subscriber->id}?locale=en&signature=bogus")
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Newsletter/Confirmed')
            ->where('status', 'expired'));

    expect($subscriber->fresh()->status)->toBe(NewsletterStatus::Pending);
});

it('captures a plan-form opt-in as pending without a second email', function () {
    $subscriber = app(NewsletterService::class)->capture([
        'email' => 'planform@example.com',
        'locale' => 'de',
        'source' => 'plan_form',
        'consent_text' => trans('newsletter.consent', [], 'de'),
    ], sendConfirmation: false);

    expect($subscriber->status)->toBe(NewsletterStatus::Pending);
    Notification::assertNothingSent();
});

it('confirms the plan-form opt-in when the user verifies their email', function () {
    $user = User::factory()->create(['email' => 'verify@example.com']);
    $plan = Plan::factory()->for($user)->create();

    NewsletterSubscriber::create([
        'email' => 'verify@example.com',
        'locale' => 'de',
        'status' => NewsletterStatus::Pending,
        'source' => 'plan_form',
    ]);

    event(new EmailVerified($user, $plan));

    $subscriber = NewsletterSubscriber::where('email', 'verify@example.com')->first();
    expect($subscriber->status)->toBe(NewsletterStatus::Confirmed);
});
