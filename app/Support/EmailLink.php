<?php

namespace App\Support;

/**
 * Appends UTM attribution to email links so clicks can be traced back to the
 * specific email (utm_campaign) and placement (utm_content).
 */
class EmailLink
{
    public static function withUtm(string $url, string $campaign, ?string $content = null): string
    {
        $params = array_filter([
            'utm_source' => 'email',
            'utm_medium' => 'notification',
            'utm_campaign' => $campaign,
            'utm_content' => $content,
        ]);

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.http_build_query($params);
    }
}
