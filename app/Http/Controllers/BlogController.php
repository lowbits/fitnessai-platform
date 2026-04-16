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
            'title' => trans('blog.meta.title'),
            'description' => trans('blog.meta.description'),
            'canonical' => $canonical,
        ];

        $labels = [
            'heading' => trans('blog.labels.heading'),
            'readMore' => trans('blog.labels.read_more'),
            'ctaHeading' => trans('blog.labels.cta_heading'),
            'ctaText' => trans('blog.labels.cta_text'),
            'ctaButton' => trans('blog.labels.cta_button'),
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => trans('blog.meta.title'),
            'description' => trans('blog.meta.description'),
            'url' => $canonical,
            'hasPart' => array_map(fn (array $post) => [
                '@type' => 'Article',
                'headline' => $post['title'],
                'description' => $post['description'],
                'url' => "{$baseUrl}{$post['url']}",
            ], $posts),
        ];

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'author' => $author,
            'meta' => $meta,
            'alternateUrls' => $alternateUrls,
            'labels' => $labels,
            'schema' => $schema,
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
                'sameAs' => [
                    'https://instagram.com/getfytrr',
                    'https://www.linkedin.com/in/tobiaslobitz/',
                ],
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Fytrr',
                'url' => config('app.url'),
            ],
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => ['[data-speakable="headline"]', '[data-speakable="summary"]'],
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

        return $schemas;
    }
}
