<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();
        $articles = config("blog.{$locale}", []);

        if (! isset($articles[$slug])) {
            abort(404);
        }

        $article = $articles[$slug];
        $author = config("blog.default_author.{$locale}");
        $baseUrl = config('app.url');

        $canonical = "{$baseUrl}/{$locale}/blog/{$slug}";
        $alternateUrls = $this->generateAlternateUrls($article['internal_slug']);

        $meta = [
            'title' => $article['title'],
            'description' => $article['description'],
            'canonical' => $canonical,
            'keywords' => $article['keywords'] ?? [],
        ];

        $schema = $this->buildSchema($article, $author, $canonical, $locale);

        return Inertia::render('Blog/Show', [
            'meta' => $meta,
            'article' => $article,
            'author' => $author,
            'alternateUrls' => $alternateUrls,
            'schema' => $schema,
            'publishedAt' => now()->parse($article['published_at'])->toFormattedDateString(),
            'lastUpdatedAt' => now()->parse($article['last_updated_at'])->toFormattedDateString(),
        ]);
    }

    private function generateAlternateUrls(string $internalSlug): array
    {
        $baseUrl = config('app.url');
        $urls = [];

        foreach (['de', 'en'] as $locale) {
            $articles = config("blog.{$locale}", []);

            foreach ($articles as $slug => $data) {
                if (($data['internal_slug'] ?? '') === $internalSlug) {
                    $urls[$locale] = "{$baseUrl}/{$locale}/blog/{$slug}";
                    break;
                }
            }
        }

        return $urls;
    }

    private function buildSchema(array $article, array $author, string $canonical, string $locale): array
    {
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['h1'],
            'description' => $article['description'],
            'url' => $canonical,
            'datePublished' => $article['published_at'],
            'dateModified' => $article['last_updated_at'],
            'inLanguage' => $locale,
            'author' => [
                '@type' => 'Person',
                'name' => $author['name'],
                'jobTitle' => $author['title'],
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Fytrr',
                'url' => config('app.url'),
            ],
        ];

        $schemas = [$articleSchema];

        if (! empty($article['faqs'])) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn (array $faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer'],
                    ],
                ], $article['faqs']),
            ];
        }

        return $schemas;
    }
}
