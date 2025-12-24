<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'currentLocale' => $locale,
            'locales' => LaravelLocalization::getSupportedLocales(),
            'server' => [
                'isLocal' => config('app.env') === 'local'
            ],
            'footerLinks' => $this->getFooterLinks($locale),
        ];
    }

    /**
     * Get footer links with translations
     */
    private function getFooterLinks(string $locale): array
    {
        $workoutPlanTypes = [
            'weight_loss',
            'muscle_gain',
            'beginner',
            'home',
            'women',
            'new_year_reset',
        ];

        // Get base path from routes translation
        $basePath = trans('routes.workout_plans_index', [], $locale);

        $links = [
            'workoutPlans' => [],
            'indexUrl' => LaravelLocalization::localizeURL("/{$basePath}", $locale),
        ];

        foreach ($workoutPlanTypes as $internalType) {
            $translatedSlug = trans("routes.type.{$internalType}", [], $locale);



            if ($translatedSlug === "routes.type.{$internalType}") {
                continue; // Translation not found
            }


            $links['workoutPlans'][$internalType] = [
                'url' => LaravelLocalization::localizeURL("/{$basePath}/{$translatedSlug}", $locale),
                'label' => trans("footer.workout_plans.{$internalType}", [], $locale),
            ];
        }

        $links['labels'] = [
            'heading' => trans('footer.workout_plans.heading', [], $locale),
            'all' => trans('footer.workout_plans.all', [], $locale),
            'product' => trans('footer.product.heading', [], $locale),
            'home' => trans('footer.product.home', [], $locale),
            'imprint' => trans('footer.legal.imprint', [], $locale),
            'language' => trans('footer.language.heading', [], $locale),
            'description' => trans('footer.description', [], $locale),
            'copyright' => trans('footer.copyright', ['year' => date('Y')], $locale),
        ];

        // Add imprint URL
        $imprintPath = trans('routes.imprint', [], $locale);
        $links['imprintUrl'] = LaravelLocalization::localizeURL("/{$imprintPath}", $locale);

        // Add language switcher data
        $links['languages'] = [];

        foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            $links['languages'][$localeCode] = [
                'name' => $properties['native'],
                'code' => $localeCode,
                'url' => LaravelLocalization::getLocalizedURL($localeCode),
                'active' => $localeCode === $locale,
            ];
        }


        return $links;
    }
}
