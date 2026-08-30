<?php

namespace App\Http\Controllers;

use App\Services\CalorieCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class CalorieCalculatorController extends Controller
{
    public function __construct(private readonly CalorieCalculatorService $service) {}

    public function __invoke(Request $request): Response
    {
        $locale = app()->getLocale();

        return Inertia::render('CalorieCalculator', [
            'preloadFonts' => ['SpaceGrotesk-latin.woff2'],
            'meta' => fn () => $this->meta($locale),
            'schema' => fn () => $this->buildSchema($locale),
            'relatedArticles' => fn () => $this->relatedArticles($locale),
            'author' => fn () => $this->author($locale),
            'internalLinks' => fn () => $this->internalLinks($locale),
            'result' => fn () => $this->resultFromRequest($request),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resultFromRequest(Request $request): ?array
    {
        $data = $request->only(['gender', 'age', 'height', 'weight', 'activity', 'goal']);

        $provided = array_filter($data, static fn ($value) => $value !== null && $value !== '');
        if ($provided === []) {
            return null;
        }

        $validator = Validator::make($data, [
            'gender' => ['required', 'in:male,female'],
            'age' => ['required', 'integer', 'min:14', 'max:100'],
            'height' => ['required', 'numeric', 'min:100', 'max:250'],
            'weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'activity' => ['required', 'in:sedentary,light,moderate,active,veryActive'],
            'goal' => ['required', 'in:lose,maintain,gain'],
        ]);

        return $validator->fails() ? null : $this->service->calculateFromArray($validator->validated());
    }

    /**
     * @return array{title: string, description: string, canonical: string}
     */
    private function meta(string $locale): array
    {
        $baseUrl = config('app.url');
        $currentPath = trans('routes.free_tools_calorie_calculator', [], $locale);

        return [
            'title' => trans('calorie_calculator.meta.title'),
            'description' => trans('calorie_calculator.meta.description'),
            'canonical' => "{$baseUrl}/{$locale}/{$currentPath}",
            'ogImage' => "{$baseUrl}/assets/images/og/kalorienrechner.webp",
            'ogImageAlt' => trans('calorie_calculator.meta.og_image_alt'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function author(string $locale): array
    {
        $author = config("blog.default_author.{$locale}", []);

        if (isset($author['image'])) {
            $author['image'] = url($author['image']);
        }

        return $author;
    }

    /**
     * @return array<int, array{id: string, url: string}>
     */
    private function internalLinks(string $locale): array
    {
        $mealPlanKey = $locale === 'de'
            ? 'routes.landing_personal_meal_plan'
            : 'routes.landing_free_workout_meal_plan';

        return [
            ['id' => 'macro', 'url' => "/{$locale}/".trans('routes.free_tools_macro_calculator', [], $locale)],
            ['id' => 'mealPlan', 'url' => "/{$locale}/".trans($mealPlanKey, [], $locale)],
            ['id' => 'workoutPlans', 'url' => "/{$locale}/".trans('routes.workout_plans_index', [], $locale)],
        ];
    }

    /**
     * @return array<int, array{url: string, title: string, description: string}>
     */
    private function relatedArticles(string $locale): array
    {
        $related = [];

        foreach (config("blog.{$locale}", []) as $slug => $article) {
            $related[] = [
                'url' => "/{$locale}/blog/{$slug}",
                'title' => $article['h1'],
                'description' => $article['description'],
            ];
        }

        return $related;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSchema(string $locale): array
    {
        $baseUrl = config('app.url');
        $currentPath = trans('routes.free_tools_calorie_calculator', [], $locale);
        $url = "{$baseUrl}/{$locale}/{$currentPath}";

        $webApp = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => trans('calorie_calculator.schema.name'),
            'url' => $url,
            'applicationCategory' => 'HealthApplication',
            'operatingSystem' => 'All',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
            ],
            'description' => trans('calorie_calculator.meta.description'),
            'inLanguage' => $locale,
            'dateModified' => trans('calorie_calculator.reviewed_date'),
        ];

        $steps = trans('calorie_calculator.howto.steps');
        $howTo = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => trans('calorie_calculator.howto.name'),
            'description' => trans('calorie_calculator.howto.description'),
            'step' => array_map(fn (array $step, int $i) => [
                '@type' => 'HowToStep',
                'position' => $i + 1,
                'name' => $step['name'],
                'text' => $step['text'],
                'url' => "{$url}#howto",
            ], $steps, array_keys($steps)),
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

        return [$webApp, $howTo, $faqSchema];
    }
}
