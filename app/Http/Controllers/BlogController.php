<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $locale = app()->getLocale();
        $articles = config("blog.{$locale}", []);
        $baseUrl = config('app.url');
        $author = config("blog.default_author.{$locale}");

        $posts = [];
        foreach ($articles as $slug => $article) {
            $posts[] = [
                'slug' => $slug,
                'title' => $article['h1'],
                'description' => $article['description'],
                'url' => "/{$locale}/blog/{$slug}",
                'og_image' => $article['og_image'] ?? null,
                'og_image_alt' => $article['og_image_alt'] ?? '',
                'published_at' => now()->parse($article['published_at'])->toFormattedDateString(),
            ];
        }

        $canonical = "{$baseUrl}/{$locale}/blog";

        $alternateUrls = [];
        foreach (['de', 'en'] as $loc) {
            $alternateUrls[$loc] = "{$baseUrl}/{$loc}/blog";
        }

        $meta = [
            'title' => $locale === 'de'
                ? 'Blog — Fitness, Ernährung & Training'
                : 'Blog — Fitness, Nutrition & Training',
            'description' => $locale === 'de'
                ? 'Ratgeber rund um Training, Ernährung und Fitness. Wissenschaftlich fundiert, praxisnah und kostenlos.'
                : 'Guides on training, nutrition and fitness. Science-based, practical and free.',
            'canonical' => $canonical,
        ];

        $labels = [
            'heading' => 'Blog',
            'readMore' => $locale === 'de' ? 'Artikel lesen' : 'Read article',
            'ctaHeading' => $locale === 'de' ? 'Von der Theorie zur Praxis' : 'From theory to practice',
            'ctaText' => $locale === 'de'
                ? 'Fytrr erstellt dir einen personalisierten Trainings- und Ernährungsplan — mit KI, in 60 Sekunden. 7 Tage kostenlos testen.'
                : 'Fytrr creates a personalised workout and nutrition plan — with AI, in 60 seconds. Try it free for 7 days.',
            'ctaButton' => $locale === 'de' ? '7 Tage kostenlos — meinen Plan erstellen' : '7 days free — create my plan',
        ];

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'author' => $author,
            'meta' => $meta,
            'alternateUrls' => $alternateUrls,
            'labels' => $labels,
        ]);
    }

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

        $ogImage = isset($article['og_image'])
            ? "{$baseUrl}{$article['og_image']}"
            : null;

        $meta = [
            'title' => $article['title'],
            'description' => $article['description'],
            'canonical' => $canonical,
            'keywords' => $article['keywords'] ?? [],
            'ogImage' => $ogImage,
            'ogImageAlt' => $article['og_image_alt'] ?? '',
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

        if (! empty($article['seo_image'])) {
            $articleSchema['image'] = [
                '@type' => 'ImageObject',
                'url' => config('app.url').$article['seo_image'],
                'caption' => $article['seo_image_alt'] ?? $article['h1'],
            ];
        }

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

        if (! empty($article['how_to_steps'])) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'HowTo',
                'name' => $article['h1'],
                'description' => $article['description'],
                'step' => array_map(fn (array $step) => [
                    '@type' => 'HowToStep',
                    'name' => $step['name'],
                    'text' => $step['text'],
                ], $article['how_to_steps']),
            ];
        }

        return $schemas;
    }
}
