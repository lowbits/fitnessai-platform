<?php

namespace App\Helpers;

use Illuminate\Support\Str;

/**
 * Builds the schema.org JSON-LD graphs emitted in the document <head>.
 *
 * Definitions live here instead of inline in the Blade layout, so the view
 * just loops over the result and adding a new graph is a one-line change to
 * forRequest(). Each builder returns a plain array ready for json_encode().
 */
class StructuredData
{
    /**
     * All JSON-LD graphs for the current request, ready to be looped over.
     *
     * @param  array<string, mixed>|null  $pageProps  Inertia page props ($page['props']).
     * @return list<array<string, mixed>>
     */
    public static function forRequest(string $locale, string $path, ?array $pageProps = null): array
    {
        return array_values(array_filter([
            self::organization(),
            self::website(),
            self::breadcrumbs($locale, $path, $pageProps),
        ]));
    }

    /** @return array<string, mixed> */
    public static function organization(): array
    {
        $url = config('app.url');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'fytrr',
            'url' => $url,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => "{$url}/apple-touch-icon.png",
                'width' => 180,
                'height' => 180,
            ],
            'foundingDate' => '2024',
            'founder' => [
                '@type' => 'Person',
                'name' => 'Tobias Lobitz',
                'jobTitle' => 'Founder & Software Developer',
                'url' => "{$url}/en/about",
                'sameAs' => [
                    'https://www.linkedin.com/in/tobiaslobitz/',
                ],
            ],
            'description' => __('meta.description'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => 'hello@fytrr.com',
                'contactType' => 'customer support',
                'availableLanguage' => ['en', 'de'],
            ],
            'sameAs' => [
                'https://instagram.com/getfytrr',
                'https://apps.apple.com/app/fytrr-ki-personal-trainer/id6757151695',
                'https://www.linkedin.com/company/fytrr',
                'https://www.wikidata.org/wiki/Q140796880',
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function website(): array
    {
        $url = config('app.url');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'fytrr',
            'url' => $url,
            'inLanguage' => ['en', 'de'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'fytrr',
                'url' => $url,
            ],
        ];
    }

    /**
     * Breadcrumb trail for the current path, or null on a locale root
     * (nothing beyond "Home" to show).
     *
     * @param  array<string, mixed>|null  $pageProps
     * @return array<string, mixed>|null
     */
    public static function breadcrumbs(string $locale, string $path, ?array $pageProps = null): ?array
    {
        $baseUrl = config('app.url');
        $segments = array_values(array_filter(explode('/', $path)));

        // Drop the locale prefix (e.g. "en", "de").
        if (isset($segments[0]) && in_array($segments[0], ['de', 'en'], true)) {
            array_shift($segments);
        }

        if ($segments === []) {
            return null;
        }

        $home = "{$baseUrl}/{$locale}";
        $crumbs = [['name' => 'Home', 'url' => $home]];
        $currentUrl = $home;

        foreach ($segments as $segment) {
            $currentUrl .= "/{$segment}";
            $crumbs[] = [
                'name' => ucfirst(str_replace(['-', '_'], ' ', $segment)),
                'url' => $currentUrl,
            ];
        }

        // Prefer a human page title (or article headline) for the final crumb.
        $last = count($crumbs) - 1;
        if (! empty($pageProps['meta']['title'])) {
            $crumbs[$last]['name'] = Str::before($pageProps['meta']['title'], ' |');
        } elseif (! empty($pageProps['article']['h1'])) {
            $crumbs[$last]['name'] = $pageProps['article']['h1'];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(
                fn (array $crumb, int $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                ],
                $crumbs,
                array_keys($crumbs),
            ),
        ];
    }
}
