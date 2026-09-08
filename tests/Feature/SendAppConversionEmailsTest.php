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

it('does not email users who already converted (password set)', function () {
    $user = User::factory()->create([
        'source' => UserSource::WEB,
        'email_verified_at' => now()->subDays(2),
    ]);
    Plan::factory()->for($user)->create();

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
