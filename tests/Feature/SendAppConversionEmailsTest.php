<?php

use App\Enums\UserSource;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\Onboarding\Email02CoachCheckin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

function webPdfUser(array $overrides = []): User
{
    $user = User::factory()->withoutPassword()->create(array_merge([
        'source' => UserSource::WEB,
        'email_verified_at' => now()->subDays(2),
    ], $overrides));

    Plan::factory()->for($user)->create();

    return $user;
}

it('emails unconverted web users two days after their free PDF', function () {
    $user = webPdfUser();

    $this->artisan('notifications:app-conversion')->assertSuccessful();

    Notification::assertSentTo($user, Email02CoachCheckin::class);
});

it('does not email users who already converted to the app', function () {
    $user = webPdfUser();
    $user->createToken('mobile'); // a live mobile token marks the user converted

    $this->artisan('notifications:app-conversion');

    Notification::assertNotSentTo($user, Email02CoachCheckin::class);
});

it('does not email outside the two-day window', function () {
    $user = webPdfUser(['email_verified_at' => now()->subDays(5)]);

    $this->artisan('notifications:app-conversion');

    Notification::assertNotSentTo($user, Email02CoachCheckin::class);
});

it('does not email mobile users', function () {
    $user = webPdfUser(['source' => UserSource::MOBILE_APPLE]);

    $this->artisan('notifications:app-conversion');

    Notification::assertNotSentTo($user, Email02CoachCheckin::class);
});

it('sends at most once per user across runs', function () {
    $user = webPdfUser();

    $this->artisan('notifications:app-conversion');
    $this->artisan('notifications:app-conversion');

    Notification::assertSentToTimes($user, Email02CoachCheckin::class, 1);
});

it('renders the app banner, install button and blog links', function () {
    $user = webPdfUser(['locale' => 'en']);
    $plan = $user->plans()->first();

    $mail = (new Email02CoachCheckin($plan))->toMail($user);

    $intro = collect($mail->introLines)->map(fn ($line) => (string) $line)->implode("\n");
    $outro = collect($mail->outroLines)->map(fn ($line) => (string) $line)->implode("\n");

    expect($mail->actionText)->toBe(__('emails.onboarding.email_02.cta'))
        ->and($mail->actionUrl)->toContain('utm_campaign=onboarding_02')
        ->and($intro)->toContain('assets/images/app/fytrr-app-home-en-top.png')
        ->and($intro)->toContain('utm_campaign=onboarding_02')
        ->and($intro)->toContain(__('emails.onboarding.email_02.feature_swap'))
        ->and($outro)->toContain(__('emails.onboarding.email_02.blog_heading'))
        ->and($outro)->toContain('/en/blog/')
        ->and($outro)->toContain('assets/images/og/thumbs/')
        ->and((string) $mail->salutation)->toContain('assets/images/mona-signature.png');
});
