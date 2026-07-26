<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mobile user on trial has active subscription', function () {
    $user = User::factory()->onTrial()->create();

    expect($user->isOnFreeTrial())->toBeTrue();
    expect($user->hasActiveSubscription())->toBeTrue();
});

test('user with expired trial has no active subscription', function () {
    $user = User::factory()->trialExpired()->create();

    expect($user->isOnFreeTrial())->toBeFalse();
    expect($user->hasActiveSubscription())->toBeFalse();
});

test('web user without trial is not on free trial', function () {
    $user = User::factory()->create();

    expect($user->isOnFreeTrial())->toBeFalse();
});

test('trial details returned in subscription details', function () {
    $user = User::factory()->onTrial()->create();

    $details = $user->getSubscriptionDetails();

    expect($details)
        ->toMatchArray([
            'has_active_subscription' => true,
            'tier' => 'trial',
            'status' => 'trial',
            'source' => 'app_trial',
        ])
        ->and($details['expires_at'])->toEqual($user->trial_ends_at);
});

test('expired trial returns free subscription details', function () {
    $user = User::factory()->trialExpired()->create();

    $details = $user->getSubscriptionDetails();

    expect($details)->toMatchArray([
        'has_active_subscription' => false,
        'tier' => 'free',
        'status' => 'free',
        'source' => 'free',
    ]);
});

test('trial_days config controls trial duration', function () {
    config(['subscription.trial_days' => 14]);

    $user = User::factory()->create([
        'trial_ends_at' => now()->addDays(config('subscription.trial_days')),
    ]);

    expect($user->trial_ends_at->startOfDay())
        ->toEqual(now()->addDays(14)->startOfDay());
    expect($user->isOnFreeTrial())->toBeTrue();
});
