<?php

use App\Enums\ConsentType;
use App\Models\User;
use App\Models\UserConsent;

test('activeFor returns the latest active consent for the type', function () {
    $user = User::factory()->create();
    UserConsent::factory()->for($user)->create(['granted_at' => now()->subDay()]);
    $latest = UserConsent::factory()->for($user)->create(['granted_at' => now()]);

    expect(UserConsent::activeFor($user, ConsentType::AiProcessing)->is($latest))->toBeTrue();
});

test('activeFor returns null once the active consent is revoked', function () {
    $user = User::factory()->create();
    UserConsent::factory()->for($user)->revoked()->create();

    expect(UserConsent::activeFor($user, ConsentType::AiProcessing))->toBeNull();
});

test('revoke stamps revoked_at without deleting the row', function () {
    $consent = UserConsent::factory()->create();

    $consent->revoke();

    expect($consent->fresh()->revoked_at)->not->toBeNull();
    expect(UserConsent::count())->toBe(1);
});

test('a fresh grant after a revoke is a new row, keeping the ledger append-only', function () {
    $user = User::factory()->create();
    UserConsent::factory()->for($user)->revoked()->create();
    UserConsent::factory()->for($user)->create();

    expect($user->consents()->count())->toBe(2)
        ->and(UserConsent::activeFor($user, ConsentType::AiProcessing))->not->toBeNull();
});
