<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestMeta
{
    /**
     * Coarse platform bucket derived from the User-Agent: ios, android or web.
     */
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

    /**
     * Two-letter country code from the Cloudflare edge header, if available.
     */
    public static function country(Request $request): ?string
    {
        $country = $request->header('CF-IPCountry');

        if ($country && strlen($country) === 2 && ! in_array($country, ['XX', 'T1'], true)) {
            return strtoupper($country);
        }

        return null;
    }
}
