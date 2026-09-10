<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class GlossaryController extends Controller
{
    public function index(): Response
    {
        $locale = app()->getLocale();

        return Inertia::render('Glossary/Index', [
            'meta' => fn () => $this->meta($locale),
            'schema' => fn () => $this->buildSchema($locale),
            'hero' => fn () => trans('glossary.hero'),
            'ui' => fn () => trans('glossary.ui'),
            'categories' => fn () => $this->categories(),
            'terms' => fn () => $this->terms(),
            'faqs' => fn () => trans('glossary.faqs'),
            'cta' => fn () => trans('glossary.cta'),
            'internalLinks' => fn () => $this->internalLinks($locale),
        ]);
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    private function categories(): array
    {
        return collect(trans('glossary.categories'))
            ->map(fn (string $label, string $id) => ['id' => $id, 'label' => $label])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{slug: string, term: string, expansion: string, definition: string, example: string, category: string}>
     */
    private function terms(): array
    {
        return collect(trans('glossary.terms'))
            ->map(fn (array $term, string $slug) => [
                'slug' => $slug,
                'term' => $term['term'],
                'expansion' => $term['expansion'] ?? '',
                'definition' => $term['definition'],
                'example' => $term['example'] ?? '',
                'category' => $term['category'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogImage: string, ogImageAlt: string}
     */
    private function meta(string $locale): array
    {
        $baseUrl = config('app.url');
        $path = trans('routes.glossary', [], $locale);

        return [
            'title' => trans('glossary.meta.title'),
            'description' => trans('glossary.meta.description'),
            'canonical' => "{$baseUrl}/{$locale}/{$path}",
            'ogImage' => "{$baseUrl}/assets/images/og/glossary.webp",
            'ogImageAlt' => trans('glossary.meta.og_image_alt'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSchema(string $locale): array
    {
        $baseUrl = config('app.url');
        $url = "{$baseUrl}/{$locale}/".trans('routes.glossary', [], $locale);

        $definedTermSet = [
            '@context' => 'https://schema.org',
            '@type' => 'DefinedTermSet',
            'name' => trans('glossary.meta.title'),
            'description' => trans('glossary.meta.description'),
            'url' => $url,
            'inLanguage' => $locale,
            'hasDefinedTerm' => collect(trans('glossary.terms'))
                ->map(fn (array $term, string $slug) => [
                    '@type' => 'DefinedTerm',
                    'name' => $this->definedTermName($term),
                    'description' => $term['definition'],
                    'url' => "{$url}#{$slug}",
                ])
                ->values()
                ->all(),
        ];

        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ], trans('glossary.faqs')),
        ];

        return [$definedTermSet, $faqSchema];
    }

    /**
     * @param  array<string, mixed>  $term
     */
    private function definedTermName(array $term): string
    {
        $expansion = $term['expansion'] ?? '';

        if ($expansion === '' || $expansion === $term['term']) {
            return $term['term'];
        }

        return "{$term['term']} ({$expansion})";
    }

    /**
     * @return array<int, array{id: string, label: string, url: string}>
     */
    private function internalLinks(string $locale): array
    {
        $routes = [
            'planRoast' => 'routes.free_tools_plan_roast',
            'calorie' => 'routes.free_tools_calorie_calculator',
            'macro' => 'routes.free_tools_macro_calculator',
            'workoutPlans' => 'routes.workout_plans_index',
        ];

        return collect($routes)
            ->map(fn (string $routeKey, string $id) => [
                'id' => $id,
                'label' => trans("glossary.links.{$id}"),
                'url' => "/{$locale}/".trans($routeKey, [], $locale),
            ])
            ->values()
            ->all();
    }
}
