<?php

use App\Contracts\NewsletterContactSync;
use App\Enums\NewsletterStatus;
use App\Events\EmailVerified;
use App\Jobs\SyncNewsletterContact;
use App\Models\NewsletterSubscriber;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\NewsletterConfirmation;
use App\Services\NewsletterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Resend\Client;
use Resend\Contracts\Transporter;
use Resend\ValueObjects\ApiKey;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;
use Resend\ValueObjects\Transporter\Payload;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

function fakeResendClient(object $captor, string $id): Client
{
    $transporter = new class($captor, $id) implements Transporter
    {
        public function __construct(private object $captor, private string $id) {}

        public function request(Payload $payload): array
        {
            $request = $payload->toRequest(
                BaseUri::from('api.resend.com'),
                Headers::withAuthorization(ApiKey::from('test-key')),
            );

            $this->captor->params = json_decode((string) $request->getBody(), true) ?? [];

            return ['id' => $this->id];
        }
    };

    return new Client($transporter);
}

it('captures an android waitlist signup as pending and sends a confirmation email', function () {
    $this->postJson('/api/newsletter/subscribe', [
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
    $this->postJson('/api/newsletter/subscribe', [
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

it('queues the resend sync instead of syncing during the request', function () {
    Bus::fake();

    $subscriber = NewsletterSubscriber::create([
        'email' => 'queued@example.com',
        'status' => NewsletterStatus::Pending,
    ]);

    app(NewsletterService::class)->confirm($subscriber);

    Bus::assertDispatched(
        SyncNewsletterContact::class,
        fn ($job) => $job->subscriber->is($subscriber),
    );
});

it('syncs an android waitlist subscriber into the android segment', function () {
    $this->app->detectEnvironment(fn () => 'production');
    config([
        'services.resend.key' => 'test-key',
        'services.resend.segments.android_waitlist' => 'seg_android',
        'services.resend.segments.default' => 'seg_general',
    ]);
    $captor = new stdClass;
    $captor->params = [];
    $this->app->instance(Client::class, fakeResendClient($captor, 'contact_123'));

    $subscriber = NewsletterSubscriber::create([
        'email' => 'sync@example.com',
        'name' => 'Sync User',
        'source' => 'android_waitlist',
        'status' => NewsletterStatus::Pending,
    ]);

    app(NewsletterContactSync::class)->sync($subscriber);

    expect($captor->params['email'])->toBe('sync@example.com')
        ->and($captor->params['segments'])->toBe([['id' => 'seg_android']])
        ->and($subscriber->fresh()->resend_contact_id)->toBe('contact_123');
});

it('syncs a newsletter subscriber into the general segment', function () {
    $this->app->detectEnvironment(fn () => 'production');
    config([
        'services.resend.key' => 'test-key',
        'services.resend.segments.android_waitlist' => 'seg_android',
        'services.resend.segments.default' => 'seg_general',
    ]);
    $captor = new stdClass;
    $captor->params = [];
    $this->app->instance(Client::class, fakeResendClient($captor, 'contact_456'));

    $subscriber = NewsletterSubscriber::create([
        'email' => 'news@example.com',
        'source' => 'waitlist',
        'status' => NewsletterStatus::Pending,
    ]);

    app(NewsletterContactSync::class)->sync($subscriber);

    expect($captor->params['segments'])->toBe([['id' => 'seg_general']]);
});

it('does not re-sync a subscriber that already has a resend contact id', function () {
    $this->app->detectEnvironment(fn () => 'production');
    config(['services.resend.key' => 'test-key']);
    $captor = new stdClass;
    $captor->params = [];
    $this->app->instance(Client::class, fakeResendClient($captor, 'contact_new'));

    $subscriber = NewsletterSubscriber::create([
        'email' => 'existing@example.com',
        'source' => 'android_waitlist',
        'status' => NewsletterStatus::Pending,
        'resend_contact_id' => 'contact_existing',
    ]);

    app(NewsletterContactSync::class)->sync($subscriber);

    expect($captor->params)->toBe([])
        ->and($subscriber->fresh()->resend_contact_id)->toBe('contact_existing');
});

it('does not sync to resend outside production', function () {
    config(['services.resend.key' => 'test-key']);
    $captor = new stdClass;
    $captor->params = [];
    $this->app->instance(Client::class, fakeResendClient($captor, 'contact_x'));

    $subscriber = NewsletterSubscriber::create([
        'email' => 'dev@example.com',
        'source' => 'android_waitlist',
        'status' => NewsletterStatus::Pending,
    ]);

    app(NewsletterContactSync::class)->sync($subscriber);

    expect($captor->params)->toBe([])
        ->and($subscriber->fresh()->resend_contact_id)->toBeNull();
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
