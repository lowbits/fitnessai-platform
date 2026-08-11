<?php

use App\Enums\UserSource;
use App\Models\User;

it('treats native app signups as mobile but not converted', function () {
    $apple = User::factory()->create(['source' => UserSource::MOBILE_APPLE]);
    $android = User::factory()->create(['source' => UserSource::MOBILE_ANDROID]);

    expect($apple->isMobileUser())->toBeTrue()
        ->and($apple->isConverted())->toBeFalse()
        ->and($android->isMobileUser())->toBeTrue()
        ->and($android->isConverted())->toBeFalse();

    expect(User::mobile()->count())->toBe(2)
        ->and(User::converted()->count())->toBe(0);
});

it('does not count a web user without a mobile token as mobile', function () {
    $web = User::factory()->create(['source' => UserSource::WEB]);

    expect($web->isMobileUser())->toBeFalse()
        ->and($web->isConverted())->toBeFalse();

    expect(User::mobile()->count())->toBe(0)
        ->and(User::converted()->count())->toBe(0);
});

it('counts a web user with a mobile token as converted', function () {
    $web = User::factory()->create(['source' => UserSource::WEB]);
    $web->createToken('mobile');

    expect($web->isMobileUser())->toBeTrue()
        ->and($web->isConverted())->toBeTrue();

    expect(User::mobile()->count())->toBe(1)
        ->and(User::converted()->count())->toBe(1);
});

it('counts a logged-out social user as converted via provider, without a token', function () {
    $web = User::factory()->create([
        'source' => UserSource::WEB,
        'provider' => 'google',
        'provider_id' => '114941339185466555627',
    ]);

    expect($web->tokens()->count())->toBe(0)
        ->and($web->isMobileUser())->toBeTrue()
        ->and($web->isConverted())->toBeTrue();

    expect(User::mobile()->count())->toBe(1)
        ->and(User::converted()->count())->toBe(1);
});

it('does not count a native social signup as converted', function () {
    $native = User::factory()->create([
        'source' => UserSource::MOBILE_APPLE,
        'provider' => 'apple',
        'provider_id' => 'apple-sub-123',
    ]);

    expect($native->isMobileUser())->toBeTrue()
        ->and($native->isConverted())->toBeFalse();

    expect(User::converted()->count())->toBe(0);
});

it('negates the mobile scope to isolate non-mobile users', function () {
    User::factory()->create(['source' => UserSource::MOBILE_APPLE]);
    $webConverted = User::factory()->create(['source' => UserSource::WEB]);
    $webConverted->createToken('mobile');
    User::factory()->create(['source' => UserSource::WEB]);

    $nonMobile = User::whereNot(fn ($query) => $query->mobile())->count();

    expect($nonMobile)->toBe(1)
        ->and(User::mobile()->count())->toBe(2);
});

it('resolves conversion from an eager-loaded tokens_count', function () {
    $web = User::factory()->create(['source' => UserSource::WEB]);
    $web->createToken('mobile');

    $loaded = User::withCount('tokens')->findOrFail($web->id);

    expect($loaded->tokens_count)->toBe(1)
        ->and($loaded->isConverted())->toBeTrue();
});
