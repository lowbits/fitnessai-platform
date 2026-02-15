<?php

use App\Enums\BodyGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    Event::fake();
});

function createDownloadAppUrl(User $user, array $extra = []): string
{
    return URL::temporarySignedRoute('download-app', now()->addHour(), array_merge([
        'locale' => 'en',
        'user' => $user->id,
    ], $extra));
}

it('renders page for valid signed URL with profile and body goal', function () {
    $user = User::factory()->withProfile([
        'body_goal' => BodyGoal::MUSCLE_GAIN,
    ])->create();

    $url = createDownloadAppUrl($user);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('userName', $user->name)
            ->where('bodyGoal', 'muscle_gain')
            ->where('appStoreUrl', config('app.app_store.ios.url'))
        );
});

it('rejects unsigned URLs with 403', function () {
    $user = User::factory()->create();

    $this->get("/en/app?user={$user->id}")
        ->assertForbidden();
});

it('generates set-password deep link for users without password', function () {
    $user = User::factory()->withoutPassword()->create();

    $url = createDownloadAppUrl($user);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('setPasswordUrl', fn ($value) => str_contains($value, '/set-password') && str_contains($value, 'signature='))
        );
});

it('does not generate set-password deep link for users with password', function () {
    $user = User::factory()->create();

    $url = createDownloadAppUrl($user);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('setPasswordUrl', null)
        );
});

it('body goal is null when user has no profile', function () {
    $user = User::factory()->create();

    $url = createDownloadAppUrl($user);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('bodyGoal', null)
        );
});

it('mobile User-Agent returns isMobile true and QR codes null', function () {
    $user = User::factory()->create();

    $url = createDownloadAppUrl($user);

    $this->get($url, ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)'])
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('isMobile', true)
            ->where('appStoreQrCode', null)
            ->where('setPasswordQrCode', null)
        );
});

it('desktop User-Agent returns isMobile false and QR codes present', function () {
    $user = User::factory()->withoutPassword()->create();

    $url = createDownloadAppUrl($user);

    $this->get($url, ['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'])
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('isMobile', false)
            ->where('appStoreQrCode', fn ($value) => str_starts_with($value, 'data:image/svg+xml;base64,'))
            ->where('setPasswordQrCode', fn ($value) => str_starts_with($value, 'data:image/svg+xml;base64,'))
        );
});

it('UTM params are passed through to Inertia props', function () {
    $user = User::factory()->create();

    $url = createDownloadAppUrl($user, [
        'utm_source' => 'email',
        'utm_medium' => 'cta',
        'utm_campaign' => 'onboarding',
    ]);

    $this->get($url)
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DownloadApp')
            ->where('utmSource', 'email')
            ->where('utmMedium', 'cta')
            ->where('utmCampaign', 'onboarding')
        );
});
