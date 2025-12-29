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

        $metaData = [
            'title' => $locale === 'de'
                ? 'Kostenlose Trainingspläne - Für jedes Ziel | fytrr.com'
                : 'Free Workout Plans - For Every Goal | fytrr.com',
            'description' => $locale === 'de'
                ? 'Entdecke kostenlose, wissenschaftlich fundierte Trainingspläne für jedes Ziel: Abnehmen, Muskelaufbau, Anfänger, Zuhause & mehr. Sofort starten!'
                : 'Discover free, science-based workout plans for every goal: Weight Loss, Muscle Gain, Beginners, Home & more. Start now!',
            'canonical' => LaravelLocalization::localizeURL("/{$basePath}", $locale),
        ];

        // Generate alternate URLs for hreflang
        $alternateUrls = [];
        foreach (['de', 'en'] as $alternateLocale) {
            $alternateBasePath = trans('routes.workout_plans_index', [], $alternateLocale);
            $alternateUrls[$alternateLocale] = LaravelLocalization::localizeURL("/{$alternateBasePath}", $alternateLocale);
        }

        // Get labels from config
        $labels = config("freeWorkouts.index_labels.{$locale}", []);

        return Inertia::render('WorkoutPlan/Index', [
            'plans' => $plans,
            'meta' => $metaData,
            'alternateUrls' => $alternateUrls,
            'labels' => $labels,
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
        if (!isset($planTypes[$type])) {
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
        $alternateUrls = $this->generateAlternateUrls($internalType);

        return Inertia::render('WorkoutPlan/Show', [
            'type' => $type,
            'meta' => $planData,
            'author' => $author,
            'reviewer' => $reviewer,
            'lastUpdated' => now()->parse($lastUpdated)->toFormattedDateString(),
            'published' => now()->parse($planData['published_at'])->toFormattedDateString(),
            'whyItWorks' => $planData['why_it_works'] ?? [],
            'commonMistakes' => $planData['common_mistakes'] ?? [],
            'workout' => $exampleWorkout,
            'faqs' => $faqs,
            'relatedPlans' => $relatedPlans,
            'alternateUrls' => $alternateUrls,
            'schema' => $this->generateSchemaMarkup($type, $planData, $exampleWorkout, $faqs, $author, $reviewer),
        ]);
    }

    /**
     * Generate alternate URLs using LaravelLocalization with translated slugs
     */
    private function generateAlternateUrls(string $internalType): array
    {
        $urls = [];

        foreach (['de', 'en'] as $locale) {
            // Get translated slug from lang files
            $translatedSlug = trans("routes.type.{$internalType}", [], $locale);

            if ($translatedSlug === "routes.type.{$internalType}") {
                // Translation not found, skip
                continue;
            }

            // Get base path from routes translation
            $basePath = trans('routes.workout_plans_index', [], $locale);
            $path = "/{$basePath}/{$translatedSlug}";

            // Use LaravelLocalization to generate proper URL
            $urls[$locale] = LaravelLocalization::localizeURL($path, $locale);
        }

        return $urls;
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
        $related = array_filter($allTypes, fn($t) => $t !== $type);
        $related = array_slice($related, 0, 3);

        return collect($related)->map(function ($relatedType) use ($planTypes) {
            $data = $planTypes[$relatedType];
            return [
                'type' => $relatedType,
                'title' => $data['h1'],
                'description' => substr($data['intro'], 0, 120) . '...',
                'url' => route('workout-plan.show', $relatedType),
            ];
        })->values()->all();
    }

    /**
     * Generate Schema.org markup for SEO
     */
    private function generateSchemaMarkup(string $type, array $planData, array $workout, array $faqs, array $author, ?array $reviewer): array
    {
        $schemaGraph = [
            // Article Schema with Author & Reviewer
            [
                '@type' => 'Article',
                'headline' => $planData['h1'],
                'description' => $planData['intro'],
                'author' => [
                    '@type' => 'Person',
                    'name' => $author['name'],
                    'jobTitle' => $author['title'],
                    'image' => url($author['image']),
                ],
                'datePublished' => now()->parse($planData['published_at'])->toIso8601String() ?? now()->toIso8601String(),
                'dateModified' => now()->parse($planData['last_updated_at'])->toIso8601String(),
            ],
            // HowTo Schema
            [
                '@type' => 'HowTo',
                'name' => $planData['h1'],
                'description' => $planData['intro'],
                'totalTime' => 'P' . $workout['weeks'] . 'W',
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
