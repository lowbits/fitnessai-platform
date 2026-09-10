<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Builds App Store links with Apple App Analytics campaign parameters so
 * downloads can be attributed in App Store Connect. Apple ignores utm_* query
 * params — attribution is carried by `ct` (campaign token, ≤ 40 chars) and the
 * optional `pt` (provider token); `mt=8` marks it as an app link.
 */
class AppStore
{
    public static function url(?string $campaign = null): string
    {
        $base = (string) config('app.app_store.ios.url');

        if ($campaign === null || $campaign === '') {
            return $base;
        }

        $params = ['mt' => '8', 'ct' => self::campaignToken($campaign)];

        if ($providerToken = config('app.app_store.ios.provider_token')) {
            $params['pt'] = $providerToken;
        }

        return $base.'?'.http_build_query($params);
    }

    private static function campaignToken(string $campaign): string
    {
        return Str::of($campaign)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-')
            ->limit(40, '')
            ->value();
    }
}
