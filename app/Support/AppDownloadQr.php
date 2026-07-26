<?php

namespace App\Support;

use App\Models\User;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class AppDownloadQr
{
    /**
     * Personalized, permanently-signed download link for a known user
     * (e.g. https://fytrr.com/de/app?user=12&...&signature=...).
     *
     * The download-app controller requires a valid signature whenever a
     * `user` param is present, so this must be signed. We use a permanent
     * signature (not temporary) because a PDF outlives a 24h email link.
     * Used for the clickable badge / text link.
     */
    public static function url(?User $user = null, string $campaign = 'plan_pdf'): string
    {
        $params = [
            'locale' => App::getLocale(),
            'utm_source' => 'pdf',
            'utm_medium' => 'plan_attachment',
            'utm_campaign' => $campaign,
        ];

        if ($user) {
            $params['user'] = $user->id;

            return URL::signedRoute('download-app', $params);
        }

        return route('download-app', $params);
    }

    /**
     * Short, unsigned URL for the QR code (https://fytrr.com/de/app?utm_source=pdf).
     * Kept minimal so the QR stays low-density and reliably scannable in print.
     */
    public static function scanUrl(): string
    {
        return route('download-app', [
            'locale' => App::getLocale(),
            'utm_source' => 'pdf',
        ]);
    }

    /**
     * Human-friendly URL for display (no scheme, no query string),
     * e.g. "fytrr.com/de/app".
     */
    public static function displayUrl(): string
    {
        $url = route('download-app', ['locale' => App::getLocale()]);

        return preg_replace('#^https?://#', '', $url);
    }

    /**
     * Generate a base64-encoded PNG data URI QR code for the given URL.
     *
     * Rendered as PNG (via GD) so it embeds reliably in DOMPDF, unlike SVG.
     */
    public static function dataUri(string $url, int $scale = 5): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'outputBase64' => true,
            'scale' => $scale,
            'quietzoneSize' => 2,
        ]);

        return (new QRCode($options))->render($url);
    }
}
