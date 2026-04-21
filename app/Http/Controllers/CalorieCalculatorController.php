<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CalorieCalculatorController extends Controller
{
    public function __invoke(): Response
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        $currentPath = trans('routes.free_tools_calorie_calculator', [], $locale);

        $meta = [
            'title' => trans('calorie_calculator.meta.title'),
            'description' => trans('calorie_calculator.meta.description'),
            'canonical' => "{$baseUrl}/{$locale}/{$currentPath}",
        ];

        $schema = $this->buildSchema($locale, $meta);

        // Build related blog article links from config
        $relatedArticles = [];
        $blogArticles = config("blog.{$locale}", []);
        foreach ($blogArticles as $slug => $article) {
            $relatedArticles[] = [
                'url' => "/{$locale}/blog/{$slug}",
                'title' => $article['h1'],
                'description' => $article['description'],
            ];
        }

        return Inertia::render('CalorieCalculator', [
            'meta' => $meta,
            'schema' => $schema,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    private function buildSchema(string $locale, array $meta): array
    {
        $baseUrl = config('app.url');
        $currentPath = trans('routes.free_tools_calorie_calculator', [], $locale);

        $webApp = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => trans('calorie_calculator.schema.name'),
            'url' => "{$baseUrl}/{$locale}/{$currentPath}",
            'applicationCategory' => 'HealthApplication',
            'operatingSystem' => 'All',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
            ],
            'description' => $meta['description'],
            'inLanguage' => $locale,
        ];

        $faqs = trans('calorie_calculator.faqs');
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ], $faqs),
        ];

        return [$webApp, $faqSchema];
    }
}
