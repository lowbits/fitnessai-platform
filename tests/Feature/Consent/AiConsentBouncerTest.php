<?php

use App\Ai\Consent\AiConsentBouncer;
use App\Ai\Consent\ConsentRequiredException;
use App\Models\User;
use App\Models\UserConsent;

test('granted is true only with an active current-version consent', function () {
    $user = User::factory()->create();
    expect(AiConsentBouncer::granted($user))->toBeFalse();

    UserConsent::factory()->for($user)->create();
    expect(AiConsentBouncer::granted($user))->toBeTrue();
});

test('granted is false for a stale version or a revoked consent', function () {
    $stale = User::factory()->create();
    UserConsent::factory()->for($stale)->create(['version' => '2020-01-01']);
    expect(AiConsentBouncer::granted($stale))->toBeFalse();

    $revoked = User::factory()->create();
    UserConsent::factory()->for($revoked)->revoked()->create();
    expect(AiConsentBouncer::granted($revoked))->toBeFalse();
});

test('permits is always true while enforcement is off, so existing users keep working', function () {
    config(['consent.enforce' => false]);

    expect(AiConsentBouncer::permits(User::factory()->create()))->toBeTrue();
});

test('permits follows consent once enforcement is armed', function () {
    config(['consent.enforce' => true]);

    expect(AiConsentBouncer::permits(User::factory()->create()))->toBeFalse();

    $consented = User::factory()->create();
    UserConsent::factory()->for($consented)->create();
    expect(AiConsentBouncer::permits($consented))->toBeTrue();
});

test('ensure throws only when enforced and consent is missing', function () {
    config(['consent.enforce' => false]);
    AiConsentBouncer::ensure(User::factory()->create());

    config(['consent.enforce' => true]);
    expect(fn () => AiConsentBouncer::ensure(User::factory()->create()))
        ->toThrow(ConsentRequiredException::class);
});
