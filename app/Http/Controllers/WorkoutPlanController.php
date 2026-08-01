<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class WorkoutPlanController extends Controller
{
    /**
     * Get plan types based on current locale
     */
    private function getPlanTypes(): array
    {
        $locale = app()->getLocale();

        return config("freeWorkouts.{$locale}", []);
    }

    /**
     * Get default author based on current locale
     */
    private function getDefaultAuthor(): array
    {
        $locale = app()->getLocale();

        return config("freeWorkouts.default_author.{$locale}", []);
    }

    /**
     * Show hub page with all plan types
     */
    public function index(): Response
    {
        $locale = app()->getLocale();
        $planTypes = $this->getPlanTypes();
        $basePath = trans('routes.workout_plans_index', [], $locale);

        $plans = collect($planTypes)->map(function ($data, $type) use ($locale, $basePath) {
            return [
                'type' => $type,
                'title' => $data['h1'],
                'description' => $data['intro'],
                'url' => LaravelLocalization::localizeURL("/{$basePath}/{$type}", $locale),
            ];
        })->values();

        // Get labels from config
        $labels = config("freeWorkouts.index_labels.{$locale}", []);

        $metaData = [
            'title' => $labels['meta_title'],
            'description' => $labels['meta_description'],
            'canonical' => LaravelLocalization::localizeURL("/{$basePath}", $locale),
        ];

        $baseUrl = config('app.url');
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $metaData['title'],
            'description' => $metaData['description'],
            'url' => $metaData['canonical'],
            'hasPart' => $plans->map(fn (array $plan) => [
                '@type' => 'Article',
                'headline' => $plan['title'],
                'description' => $plan['description'],
                'url' => $baseUrl.$plan['url'],
            ])->values()->all(),
        ];

        return Inertia::render('WorkoutPlan/Index', [
            'plans' => $plans,
            'meta' => $metaData,
            'labels' => $labels,
            'schema' => $schema,
        ]);
    }

    /**
     * Show specific workout plan type
     */
    public function show(string $type): Response
    {
        $locale = app()->getLocale();
        $planTypes = $this->getPlanTypes();

        // Check if type exists in current locale
        if (! isset($planTypes[$type])) {
            abort(404);
        }

        $planData = $planTypes[$type];
        $internalType = $planData['internal_type'];
        $author = $planData['author'] ?? $this->getDefaultAuthor();
        $reviewer = $planData['reviewer'] ?? null;
        $lastUpdated = $planData['last_updated_at'] ?? now();

        // Generate canonical URL dynamically based on current locale and type
        $basePath = trans('routes.workout_plans_index', [], $locale);
        $planData['canonical'] = LaravelLocalization::localizeURL("/{$basePath}/{$type}", $locale);

        $exampleWorkout = $this->getExampleWorkout($internalType, $locale);
        $faqs = $this->getFAQs($internalType, $locale);
        $relatedPlans = $this->getRelatedPlans($type, $locale);

        return Inertia::render('WorkoutPlan/Show', [
            'type' => $type,
            'meta' => $planData,
            'author' => $author,
            'reviewer' => $reviewer,
            'lastUpdated' => now()->parse($lastUpdated)->locale($locale)->isoFormat('LL'),
            'published' => now()->parse($planData['published_at'])->locale($locale)->isoFormat('LL'),
            'whyItWorks' => $planData['why_it_works'] ?? [],
            'commonMistakes' => $planData['common_mistakes'] ?? [],
            'workout' => $exampleWorkout,
            'faqs' => $faqs,
            'relatedPlans' => $relatedPlans,
            'sources' => $planData['sources'] ?? [],
            'schema' => $this->generateSchemaMarkup($type, $planData, $exampleWorkout, $faqs, $author, $reviewer),
        ]);
    }

    /**
     * Get example workout structure for plan type
     */
    private function getExampleWorkout(string $internalType, string $locale): array
    {
        $planTypes = $this->getPlanTypes();

        // Find plan by internal_type
        foreach ($planTypes as $plan) {
            if (($plan['internal_type'] ?? null) === $internalType) {
                return $plan['workout'] ?? [];
            }
        }

        // Fallback to first plan's workout
        return collect($planTypes)->first()['workout'] ?? [];
    }

    /**
     * Get FAQs for plan type
     */
    private function getFAQs(string $internalType, string $locale): array
    {
        $planTypes = $this->getPlanTypes();

        // Find plan by internal_type
        foreach ($planTypes as $plan) {
            if (($plan['internal_type'] ?? null) === $internalType) {
                return $plan['faqs'] ?? [];
            }
        }

        // Fallback to first plan's FAQs
        return collect($planTypes)->first()['faqs'] ?? [];
    }

    /**
     * Get related workout plans
     */
    private function getRelatedPlans(string $type, string $locale): array
    {
        $planTypes = $this->getPlanTypes();
        $allTypes = array_keys($planTypes);
        $related = array_filter($allTypes, fn ($t) => $t !== $type);
        $related = array_slice($related, 0, 3);

        return collect($related)->map(function ($relatedType) use ($planTypes) {
            $data = $planTypes[$relatedType];

            return [
                'type' => $relatedType,
                'title' => $data['h1'],
                'description' => substr($data['intro'], 0, 120).'...',
                'url' => route('workout-plan.show', $relatedType),
            ];
        })->values()->all();
    }

    /**
     * Generate Schema.org markup for SEO
     */
    private function generateSchemaMarkup(string $type, array $planData, array $workout, array $faqs, array $author, ?array $reviewer): array
    {
        $baseUrl = config('app.url');

        $canonical = str_starts_with($planData['canonical'], 'http')
            ? $planData['canonical']
            : $baseUrl.$planData['canonical'];

        $publisher = [
            '@type' => 'Organization',
            'name' => 'fytrr',
            'url' => $baseUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $baseUrl.'/apple-touch-icon.png',
                'width' => 180,
                'height' => 180,
            ],
        ];

        $schemaGraph = [
            // Article Schema with Author & Reviewer
            [
                '@type' => 'Article',
                'headline' => $planData['h1'],
                'description' => $planData['intro'],
                'url' => $canonical,
                'mainEntityOfPage' => $canonical,
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => $baseUrl.'/fitness-plan.png',
                    'width' => 1536,
                    'height' => 1024,
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => $author['name'],
                    'jobTitle' => $author['title'],
                    'image' => url($author['image']),
                    'url' => $baseUrl.'/en/about',
                    'worksFor' => [
                        '@type' => 'Organization',
                        'name' => 'fytrr',
                        'url' => $baseUrl,
                    ],
                    'knowsAbout' => ['strength training', 'fitness', 'nutrition planning', 'workout programming'],
                    'sameAs' => [
                        'https://instagram.com/getfytrr',
                        'https://www.linkedin.com/in/tobiaslobitz/',
                    ],
                ],
                'publisher' => $publisher,
                'datePublished' => now()->parse($planData['published_at'])->toIso8601String() ?? now()->toIso8601String(),
                'dateModified' => now()->parse($planData['last_updated_at'])->toIso8601String(),
                'speakable' => [
                    '@type' => 'SpeakableSpecification',
                    'cssSelector' => ['[data-speakable="headline"]', '[data-speakable="summary"]'],
                ],
            ],
            // HowTo Schema
            [
                '@type' => 'HowTo',
                'name' => $planData['h1'],
                'description' => $planData['intro'],
                'totalTime' => 'P'.$workout['weeks'].'W',
            ],
            // FAQ Schema
            [
                '@type' => 'FAQPage',
                'mainEntity' => collect($faqs)->map(function ($faq) {
                    return [
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer'],
                        ],
                    ];
                })->all(),
            ],
        ];

        // Add Reviewer if available
        if ($reviewer) {
            $schemaGraph[0]['reviewedBy'] = [
                '@type' => 'Person',
                'name' => $reviewer['name'],
                'jobTitle' => $reviewer['title'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $schemaGraph,
        ];
    }
}
