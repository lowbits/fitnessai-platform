<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate the sitemap with all pages including workout plans';

    public function handle()
    {
        $this->info('Starting sitemap generation...');

        $sitemap = Sitemap::create();
        $locales = ['de', 'en'];
        $baseUrl = config('app.url');

        foreach ($locales as $locale) {
            app()->setLocale($locale);

            $this->info("Processing locale: {$locale}");

            // Homepage
            $homepageUrl = Url::create("/{$locale}")
                ->setLastModificationDate(Carbon::now())
                ->setPriority(1.0)
                ->setChangeFrequency('weekly');

            // Add alternates for homepage
            foreach ($locales as $altLocale) {
                if ($altLocale !== $locale) {
                    $homepageUrl->addAlternate("{$baseUrl}/{$altLocale}", $altLocale);
                }
            }

            $sitemap->add($homepageUrl);

            // App page
            $appUrl = Url::create("/{$locale}/app")
                ->setLastModificationDate(Carbon::now())
                ->setPriority(0.8)
                ->setChangeFrequency('monthly');

            foreach ($locales as $altLocale) {
                if ($altLocale !== $locale) {
                    $appUrl->addAlternate("{$baseUrl}/{$altLocale}/app", $altLocale);
                }
            }

            $sitemap->add($appUrl);

            $this->info("Added app page: /{$locale}/app");

            // Calorie Calculator
            $calcPath = trans('routes.free_tools_calorie_calculator', [], $locale);
            $calcUrl = Url::create("/{$locale}/{$calcPath}")
                ->setLastModificationDate(Carbon::now())
                ->setPriority(0.9)
                ->setChangeFrequency('monthly');

            foreach ($locales as $altLocale) {
                if ($altLocale !== $locale) {
                    $altCalcPath = trans('routes.free_tools_calorie_calculator', [], $altLocale);
                    $calcUrl->addAlternate("{$baseUrl}/{$altLocale}/{$altCalcPath}", $altLocale);
                }
            }

            $sitemap->add($calcUrl);

            $this->info("Added calorie calculator: /{$locale}/{$calcPath}");

            // Workout Plans
            $planTypes = $this->getPlanTypes();

            if (! empty($planTypes)) {
                $basePath = trans('routes.workout_plans_index', [], $locale);

                // Index Page - use latest update from all plans
                $latestUpdate = $this->getLatestPlanUpdate($planTypes);

                $indexUrl = Url::create("/{$locale}/{$basePath}")
                    ->setLastModificationDate($latestUpdate)
                    ->setPriority(0.9)
                    ->setChangeFrequency('weekly');

                // Add alternates for index page
                foreach ($locales as $altLocale) {
                    if ($altLocale !== $locale) {
                        $altBasePath = trans('routes.workout_plans_index', [], $altLocale);
                        $indexUrl->addAlternate("{$baseUrl}/{$altLocale}/{$altBasePath}", $altLocale);
                    }
                }

                $sitemap->add($indexUrl);

                $this->info("Added workout plans index: /{$locale}/{$basePath}");

                // Individual Plan Pages
                foreach ($planTypes as $type => $data) {
                    $lastMod = $this->getPlanLastModified($data);

                    $url = Url::create("/{$locale}/{$basePath}/{$type}")
                        ->setLastModificationDate($lastMod)
                        ->setPriority(0.8)
                        ->setChangeFrequency('monthly');

                    // Add hreflang alternates for same workout plan in other languages
                    if (isset($data['internal_type'])) {
                        $alternates = $this->getAlternateUrls($data['internal_type'], $locale);
                        foreach ($alternates as $altLocale => $altUrl) {
                            $url->addAlternate($altUrl, $altLocale);
                        }
                    }

                    $sitemap->add($url);

                    $this->info("Added plan: {$type}");
                }
            }

            // Blog Index
            $blogIndexUrl = Url::create("/{$locale}/blog")
                ->setLastModificationDate(Carbon::now())
                ->setPriority(0.8)
                ->setChangeFrequency('weekly');

            foreach ($locales as $altLocale) {
                if ($altLocale !== $locale) {
                    $blogIndexUrl->addAlternate("{$baseUrl}/{$altLocale}/blog", $altLocale);
                }
            }

            $sitemap->add($blogIndexUrl);

            $this->info("Added blog index: /{$locale}/blog");

            // Blog Articles
            $blogArticles = config("blog.{$locale}", []);

            foreach ($blogArticles as $slug => $articleData) {
                $lastMod = $this->getPlanLastModified($articleData);

                $blogUrl = Url::create("/{$locale}/blog/{$slug}")
                    ->setLastModificationDate($lastMod)
                    ->setPriority(0.8)
                    ->setChangeFrequency('monthly');

                // Add hreflang alternates
                if (isset($articleData['internal_slug'])) {
                    foreach ($locales as $altLocale) {
                        if ($altLocale === $locale) {
                            continue;
                        }
                        $altArticles = config("blog.{$altLocale}", []);
                        foreach ($altArticles as $altSlug => $altData) {
                            if (($altData['internal_slug'] ?? '') === $articleData['internal_slug']) {
                                $blogUrl->addAlternate("{$baseUrl}/{$altLocale}/blog/{$altSlug}", $altLocale);
                                break;
                            }
                        }
                    }
                }

                $sitemap->add($blogUrl);

                $this->info("Added blog article: /{$locale}/blog/{$slug}");
            }

            // Legal Pages
            $legalPageMappings = [
                'de' => [
                    'agb' => 'terms-and-conditions',
                    'datenschutz' => 'privacy-policy',
                ],
                'en' => [
                    'terms-and-conditions' => 'agb',
                    'privacy-policy' => 'datenschutz',
                ],
            ];

            if (isset($legalPageMappings[$locale])) {
                foreach ($legalPageMappings[$locale] as $page => $alternatePage) {
                    $legalUrl = Url::create("/{$locale}/{$page}")
                        ->setLastModificationDate(Carbon::now()->subMonths(1))
                        ->setPriority(0.3)
                        ->setChangeFrequency('yearly');

                    // Add alternate for the other language
                    $altLocale = $locale === 'de' ? 'en' : 'de';
                    $legalUrl->addAlternate("{$baseUrl}/{$altLocale}/{$alternatePage}", $altLocale);

                    $sitemap->add($legalUrl);
                }

                $this->info("Added legal pages for {$locale}");
            }
        }

        // Save sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ Sitemap generated successfully!');
        $this->info('📍 Location: '.public_path('sitemap.xml'));
        $this->info('🔗 URL: '.$baseUrl.'/sitemap.xml');

        return Command::SUCCESS;
    }

    /**
     * Get plan types from config for current locale
     */
    private function getPlanTypes(): array
    {
        $locale = app()->getLocale();

        return config("freeWorkouts.{$locale}", []);
    }

    /**
     * Get last modification date for a plan
     */
    private function getPlanLastModified(array $planData): Carbon
    {
        // Priority: last_updated_at > published_at > now
        if (isset($planData['last_updated_at'])) {
            try {
                return Carbon::parse($planData['last_updated_at']);
            } catch (\Exception $e) {
                // Fallback if date parsing fails
            }
        }

        if (isset($planData['published_at'])) {
            try {
                return Carbon::parse($planData['published_at']);
            } catch (\Exception $e) {
                // Fallback if date parsing fails
            }
        }

        return Carbon::now();
    }

    /**
     * Get the latest update date from all plans
     */
    private function getLatestPlanUpdate(array $planTypes): Carbon
    {
        $latest = Carbon::now()->subYear(); // Fallback to 1 year ago

        foreach ($planTypes as $data) {
            $planDate = $this->getPlanLastModified($data);

            if ($planDate->gt($latest)) {
                $latest = $planDate;
            }
        }

        return $latest;
    }

    /**
     * Get alternate language URLs for the same workout plan
     * Matches plans by internal_type across different locales
     */
    private function getAlternateUrls(string $internalType, string $currentLocale): array
    {
        $baseUrl = config('app.url');
        $alternates = [];
        $locales = ['de', 'en'];

        foreach ($locales as $locale) {
            if ($locale === $currentLocale) {
                continue; // Skip current locale
            }

            $plans = config("freeWorkouts.{$locale}", []);

            // Find the plan with matching internal_type
            foreach ($plans as $slug => $data) {
                if (isset($data['internal_type']) && $data['internal_type'] === $internalType) {
                    $basePath = trans('routes.workout_plans_index', [], $locale);
                    $alternates[$locale] = "{$baseUrl}/{$locale}/{$basePath}/{$slug}";
                    break;
                }
            }
        }

        return $alternates;
    }
}
