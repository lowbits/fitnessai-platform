<?php

namespace App\Http\Controllers;

use App\Services\MacroCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class MacroCalculatorController extends Controller
{
    public function __construct(private readonly MacroCalculatorService $service) {}

    /**
     * Renders the page and also serves live updates: the frontend refreshes the
     * `result` prop with a partial reload (`router.reload({ only: ['result'] })`),
     * so the SSR seed and every live recalculation share one code path — no
     * separate endpoint, and the numbers always come from the product's
     * Metabolism helper.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        return Inertia::render('MacroCalculator', [
            'preloadFonts' => ['SpaceGrotesk-latin.woff2'],
            // Wrapped in closures so a partial reload for `result` skips them.
            'meta' => fn () => $this->meta($locale),
            'schema' => fn () => $this->buildSchema($locale),
            'relatedArticles' => fn () => $this->relatedArticles($locale),
            'author' => fn () => $this->author($locale),
            'internalLinks' => fn () => $this->internalLinks($locale),
            // Null until the visitor completes the form; refreshed via
            // `router.reload({ only: ['result'] })` on every input change.
            'result' => fn () => $this->resultFromRequest($request),
        ]);
    }

    /**
     * The site's canonical author (shared with blog & workout-plan pages) for
     * the E-E-A-T byline.
     *
     * @return array<string, string>
     */
    private function author(string $locale): array
    {
        $author = config("freeWorkouts.default_author.{$locale}", []);

        if (isset($author['image'])) {
            $author['image'] = url($author['image']);
        }

        return $author;
    }

    /**
     * Descriptive internal links to related tools, localized.
     *
     * @return array<int, array{id: string, url: string}>
     */
    private function internalLinks(string $locale): array
    {
        $mealPlanKey = $locale === 'de'
            ? 'routes.landing_personal_meal_plan'
            : 'routes.landing_free_workout_meal_plan';

        return [
            ['id' => 'calorie', 'url' => "/{$locale}/".trans('routes.free_tools_calorie_calculator', [], $locale)],
            ['id' => 'mealPlan', 'url' => "/{$locale}/".trans($mealPlanKey, [], $locale)],
            ['id' => 'workoutPlans', 'url' => "/{$locale}/".trans('routes.workout_plans_index', [], $locale)],
        ];
    }

    /**
     * Compute the macro result from the request, or null when the request
     * carries no (or invalid) calculator data — e.g. the initial page load,
     * where the form starts empty and no result is shown yet.
     *
     * @return array<string, mixed>|null
     */
    private function resultFromRequest(Request $request): ?array
    {
        $data = $request->only(['gender', 'age', 'height', 'weight', 'activity', 'sessions', 'goal', 'diet']);

        $provided = array_filter($data, static fn ($value) => $value !== null && $value !== '');
        if ($provided === []) {
            return null;
        }

        $validator = Validator::make($data, [
            'gender' => ['required', 'in:male,female'],
            'age' => ['required', 'integer', 'min:14', 'max:100'],
            'height' => ['required', 'numeric', 'min:100', 'max:250'],
            'weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'activity' => ['required', 'in:mainly_sitting,mainly_standing,mainly_walking,hard_working'],
            'sessions' => ['required', 'integer', 'min:0', 'max:7'],
            'goal' => ['required', 'in:lose,maintain,gain'],
            'diet' => ['required', 'in:omnivore,vegetarian,pescatarian,vegan'],
        ]);

        return $validator->fails() ? null : $this->service->calculateFromArray($validator->validated());
    }

    /**
     * @return array{title: string, description: string, canonical: string}
     */
    private function meta(string $locale): array
    {
        $baseUrl = config('app.url');
        $currentPath = trans('routes.free_tools_macro_calculator', [], $locale);

        return [
            'title' => trans('macro_calculator.meta.title'),
            'description' => trans('macro_calculator.meta.description'),
            'canonical' => "{$baseUrl}/{$locale}/{$currentPath}",
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
        $currentPath = trans('routes.free_tools_macro_calculator', [], $locale);

        $webApp = [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => trans('macro_calculator.schema.name'),
            'url' => "{$baseUrl}/{$locale}/{$currentPath}",
            'applicationCategory' => 'HealthApplication',
            'operatingSystem' => 'All',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
            ],
            'description' => trans('macro_calculator.meta.description'),
            'inLanguage' => $locale,
            'dateModified' => trans('macro_calculator.reviewed_date'),
        ];

        $faqs = trans('macro_calculator.faqs');
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
