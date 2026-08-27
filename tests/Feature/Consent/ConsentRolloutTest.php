<?php

use App\Support\ConsentRollout;

beforeEach(fn () => config(['consent.min_app_version' => '2.2.0']));

test('clients without a reported version keep generating at signup', function () {
    expect(ConsentRollout::clientCollectsConsent(null))->toBeFalse();
});

test('pre-consent app versions do not collect consent', function () {
    expect(ConsentRollout::clientCollectsConsent('2.1.0'))->toBeFalse()
        ->and(ConsentRollout::clientCollectsConsent('2.0.0'))->toBeFalse();
});

test('the consent release and newer collect consent', function () {
    expect(ConsentRollout::clientCollectsConsent('2.2.0'))->toBeTrue()
        ->and(ConsentRollout::clientCollectsConsent('2.3.1'))->toBeTrue();
});
