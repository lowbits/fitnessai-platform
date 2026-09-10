<?php

use App\Support\AppStore;

it('returns the plain url without a campaign', function () {
    config(['app.app_store.ios.url' => 'https://apps.apple.com/app/id123']);

    expect(AppStore::url())->toBe('https://apps.apple.com/app/id123')
        ->and(AppStore::url(''))->toBe('https://apps.apple.com/app/id123');
});

it('appends a sanitized campaign token and mt=8', function () {
    config([
        'app.app_store.ios.url' => 'https://apps.apple.com/app/id123',
        'app.app_store.ios.provider_token' => null,
    ]);

    expect(AppStore::url('email-plan_ready'))
        ->toBe('https://apps.apple.com/app/id123?mt=8&ct=email-plan-ready');
});

it('includes the provider token when configured', function () {
    config([
        'app.app_store.ios.url' => 'https://apps.apple.com/app/id123',
        'app.app_store.ios.provider_token' => '999',
    ]);

    expect(AppStore::url('email'))
        ->toBe('https://apps.apple.com/app/id123?mt=8&ct=email&pt=999');
});
