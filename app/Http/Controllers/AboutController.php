<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(): Response
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');

        $canonical = "{$baseUrl}/{$locale}/".trans('routes.about', [], $locale);

        $alternateUrls = [];
        foreach (['de', 'en'] as $loc) {
            $alternateUrls[$loc] = "{$baseUrl}/{$loc}/".trans('routes.about', [], $loc);
        }

        $meta = [
            'title' => trans('about.meta.title'),
            'description' => trans('about.meta.description'),
            'keywords' => trans('about.meta.keywords'),
            'canonical' => $canonical,
            'ogImage' => "{$baseUrl}/assets/images/og/ueber-uns-og.webp",
            'ogImageAlt' => trans('about.meta.og_image_alt'),
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => 'Tobias Lobitz',
                'jobTitle' => trans('about.schema.job_title'),
                'description' => trans('about.schema.description'),
                'sameAs' => [
                    'https://instagram.com/getfytrr',
                    'https://www.linkedin.com/in/tobiaslobitz/',
                ],
                'worksFor' => [
                    '@type' => 'Organization',
                    'name' => 'Fytrr',
                    'url' => $baseUrl,
                ],
                'knowsAbout' => trans('about.schema.knows_about'),
            ],
        ];

        return Inertia::render('About', [
            'meta' => $meta,
            'alternateUrls' => $alternateUrls,
            'schema' => $schema,
            'appStoreUrl' => config('app.app_store.ios.url'),
            'authorImage' => '/assets/authors/tobias.avif',
            'credentials' => trans('about.credentials'),
        ]);
    }
}
