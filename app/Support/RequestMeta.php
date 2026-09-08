<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestMeta
{
    public static function platform(?string $userAgent): string
    {
        $userAgent ??= '';

        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'ios';
        }

        if (preg_match('/Android/i', $userAgent)) {
            return 'android';
        }

        return 'web';
    }

    public static function country(Request $request): ?string
    {
        $country = $request->header('CF-IPCountry');

        if ($country && strlen($country) === 2 && ! in_array($country, ['XX', 'T1'], true)) {
            return strtoupper($country);
        }

        foreach (explode(',', (string) $request->header('Accept-Language')) as $part) {
            $tag = trim(explode(';', $part)[0]);

            if (preg_match('/-([A-Za-z]{2})$/', $tag, $matches)) {
                return strtoupper($matches[1]);
            }
        }

        return null;
    }
}
