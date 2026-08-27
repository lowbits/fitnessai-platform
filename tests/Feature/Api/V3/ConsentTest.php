<?php

use App\Enums\ConsentSource;
use App\Enums\ConsentType;
use App\Events\AiConsentGranted;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function grantPayload(array $overrides = []): array
{
    return array_merge([
        'type' => ConsentType::AiProcessing->value,
        'version' => config('consent.current_version'),
        'source' => ConsentSource::Onboarding->value,
        'locale' => 'de',
    ], $overrides);
}

test('the endpoints require authentication', function () {
    getJson('/api/v3/consent/current')->assertUnauthorized();
    postJson('/api/v3/consent', grantPayload())->assertUnauthorized();
    deleteJson('/api/v3/consent', ['type' => ConsentType::AiProcessing->value])->assertUnauthorized();
});

test('current returns version, providers and the copy for the user locale', function () {
    Sanctum::actingAs(User::factory()->withLocale('de')->create());

    getJson('/api/v3/consent/current')
        ->assertOk()
        ->assertJsonPath('version', config('consent.current_version'))
        ->assertJsonPath('providers', ['OpenAI', 'Mistral AI'])
        ->assertJsonPath('copy.onboarding.title', 'Bevor dein Plan entsteht');
});

test('current falls back to English copy for an English user', function () {
    Sanctum::actingAs(User::factory()->withLocale('en')->create());

    getJson('/api/v3/consent/current')
        ->assertJsonPath('copy.onboarding.title', 'Before your plan is created');
});

test('storing consent appends a row and echoes the version', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    postJson('/api/v3/consent', grantPayload())
        ->assertCreated()
        ->assertJsonPath('version', config('consent.current_version'));

    expect(UserConsent::activeFor($user, ConsentType::AiProcessing)?->version)
        ->toBe(config('consent.current_version'));
});

test('granting consent dispatches the AiConsentGranted event', function () {
    Event::fake([AiConsentGranted::class]);
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/v3/consent', grantPayload())->assertCreated();

    Event::assertDispatched(AiConsentGranted::class);
});

test('storing consent with a stale version is rejected', function () {
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/v3/consent', grantPayload(['version' => '2020-01-01']))
        ->assertStatus(422)
        ->assertJsonValidationErrors('version');
});

test('destroying consent revokes the active row', function () {
    $user = User::factory()->create();
    UserConsent::factory()->for($user)->create();
    Sanctum::actingAs($user);

    deleteJson('/api/v3/consent', ['type' => ConsentType::AiProcessing->value])
        ->assertOk()
        ->assertJsonPath('version', null);

    expect(UserConsent::activeFor($user, ConsentType::AiProcessing))->toBeNull();
});

test('an AI route returns 409 consent_required when enforcement is armed and consent is missing', function () {
    config(['consent.enforce' => true]);
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/v3/coach/messages', ['message' => 'hi'])
        ->assertStatus(409)
        ->assertJsonPath('code', 'consent_required');
});

test('me exposes the active consent version and the current server version', function () {
    $user = User::factory()->withProfile()->create();
    Sanctum::actingAs($user);

    getJson('/api/v3/auth/me')
        ->assertOk()
        ->assertJsonPath('consent.current_version', config('consent.current_version'))
        ->assertJsonPath('consent.ai_processing.version', null)
        ->assertJsonPath('consent.ai_processing.required', true);

    UserConsent::factory()->for($user)->create();

    getJson('/api/v3/auth/me')
        ->assertJsonPath('consent.ai_processing.version', config('consent.current_version'))
        ->assertJsonPath('consent.ai_processing.required', false);
});
