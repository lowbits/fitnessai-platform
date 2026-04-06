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

        $meta = $locale === 'de'
            ? [
                'title' => 'Über Fytrr — Die Geschichte hinter deinem KI Personal Coach',
                'description' => 'Fytrr macht Personal Training bezahlbar. Gegründet von Tobias Lobitz — Entwickler, Fitness-Enthusiast, 15 Jahre Trainingserfahrung. Lerne uns kennen.',
                'keywords' => ['fytrr', 'über uns', 'personal training app', 'ki personal coach', 'fitness app', 'tobias lobitz'],
            ]
            : [
                'title' => 'About Fytrr — The Story Behind Your AI Personal Coach',
                'description' => 'Fytrr makes personal training affordable. Founded by Tobias Lobitz — developer, fitness enthusiast, 15 years of training experience. Get to know us.',
                'keywords' => ['fytrr', 'about us', 'personal training app', 'ai personal coach', 'fitness app', 'tobias lobitz'],
            ];

        $meta['canonical'] = $canonical;
        $meta['ogImage'] = "{$baseUrl}/assets/images/og/ueber-uns-og.webp";
        $meta['ogImageAlt'] = $locale === 'de'
            ? 'Fytrr — Dein KI Personal Coach'
            : 'Fytrr — Your AI Personal Coach';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => 'Tobias Lobitz',
                'jobTitle' => $locale === 'de' ? 'Gründer & Entwickler' : 'Founder & Developer',
                'description' => $locale === 'de'
                    ? 'Entwickler und Fitness-Enthusiast mit über 15 Jahren Trainingserfahrung. Baut seit 2018 Fitness-Apps.'
                    : 'Developer and fitness enthusiast with over 15 years of training experience. Building fitness apps since 2018.',
                'worksFor' => [
                    '@type' => 'Organization',
                    'name' => 'Fytrr',
                    'url' => $baseUrl,
                ],
                'knowsAbout' => [
                    'Fitness',
                    'Krafttraining',
                    $locale === 'de' ? 'Ernährung' : 'Nutrition',
                    'App-Entwicklung',
                    $locale === 'de' ? 'Künstliche Intelligenz' : 'Artificial Intelligence',
                ],
            ],
        ];

        return Inertia::render('About', [
            'meta' => $meta,
            'alternateUrls' => $alternateUrls,
            'schema' => $schema,
            'appStoreUrl' => config('app.app_store.ios.url'),
            'authorImage' => '/assets/authors/tobias.avif',
        ]);
    }
}
