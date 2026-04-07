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

    /** @var string[] */
    private const LOCALES = ['de', 'en'];

    /**
     * Static pages to include in the sitemap.
     * When adding a new page, just add an entry here.
     *
     * - 'route_key': translation key from lang/xx/routes.php (null for fixed paths)
     * - 'path': fixed path segment (ignored when route_key is set)
     * - 'priority': sitemap priority (0.0–1.0)
     * - 'changeFrequency': how often the page changes
     *
     * @return array<int, array{route_key: string|null, path: string, priority: float, changeFrequency: string}>
     */
    private function staticPages(): array
    {
        return [
            ['route_key' => null, 'path' => '', 'priority' => 1.0, 'changeFrequency' => 'weekly'],
            ['route_key' => null, 'path' => 'app', 'priority' => 0.8, 'changeFrequency' => 'monthly'],
            ['route_key' => 'routes.about', 'path' => '', 'priority' => 0.7, 'changeFrequency' => 'monthly'],
            ['route_key' => 'routes.free_tools_calorie_calculator', 'path' => '', 'priority' => 0.9, 'changeFrequency' => 'monthly'],
            ['route_key' => 'routes.imprint', 'path' => '', 'priority' => 0.3, 'changeFrequency' => 'yearly'],
            ['route_key' => 'routes.data_privacy', 'path' => '', 'priority' => 0.3, 'changeFrequency' => 'yearly'],
            ['route_key' => 'routes.terms', 'path' => '', 'priority' => 0.3, 'changeFrequency' => 'yearly'],
            ['route_key' => 'routes.disclaimer', 'path' => '', 'priority' => 0.3, 'changeFrequency' => 'yearly'],
        ];
    }

    public function handle(): int
    {
        $this->info('Starting sitemap generation...');

        $sitemap = Sitemap::create();
        $baseUrl = config('app.url');

        foreach (self::LOCALES as $locale) {
            app()->setLocale($locale);
            $this->info("Processing locale: {$locale}");

            $this->addStaticPages($sitemap, $locale, $baseUrl);
            $this->addWorkoutPlans($sitemap, $locale, $baseUrl);
            $this->addBlogPages($sitemap, $locale, $baseUrl);
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at '.$baseUrl.'/sitemap.xml');

        return Command::SUCCESS;
    }

    private function addStaticPages(Sitemap $sitemap, string $locale, string $baseUrl): void
    {
        foreach ($this->staticPages() as $page) {
            $fullPath = $this->buildLocalizedPath($locale, $page['route_key'], $page['path']);

            $url = Url::create($fullPath)
                ->setLastModificationDate(Carbon::now())
                ->setPriority($page['priority'])
                ->setChangeFrequency($page['changeFrequency']);

            $this->addAlternates($url, $locale, $baseUrl, $page['route_key'], $page['path']);

            $sitemap->add($url);
            $this->info("  Added: {$fullPath}");
        }
    }

    private function addWorkoutPlans(Sitemap $sitemap, string $locale, string $baseUrl): void
    {
        $planTypes = config("freeWorkouts.{$locale}", []);

        if (empty($planTypes)) {
            return;
        }

        $basePath = trans('routes.workout_plans_index', [], $locale);

        // Index page
        $indexUrl = Url::create("/{$locale}/{$basePath}")
            ->setLastModificationDate($this->getLatestPlanUpdate($planTypes))
            ->setPriority(0.9)
            ->setChangeFrequency('weekly');

        $this->addAlternates($indexUrl, $locale, $baseUrl, 'routes.workout_plans_index');

        $sitemap->add($indexUrl);
        $this->info("  Added: /{$locale}/{$basePath}");

        // Individual plan pages
        foreach ($planTypes as $type => $data) {
            $url = Url::create("/{$locale}/{$basePath}/{$type}")
                ->setLastModificationDate($this->parseDate($data))
                ->setPriority(0.8)
                ->setChangeFrequency('monthly');

            if (isset($data['internal_type'])) {
                $this->addWorkoutPlanAlternates($url, $data['internal_type'], $locale, $baseUrl);
            }

            $sitemap->add($url);
            $this->info("  Added: /{$locale}/{$basePath}/{$type}");
        }
    }

    private function addBlogPages(Sitemap $sitemap, string $locale, string $baseUrl): void
    {
        // Blog index
        $blogIndexUrl = Url::create("/{$locale}/blog")
            ->setLastModificationDate(Carbon::now())
            ->setPriority(0.8)
            ->setChangeFrequency('weekly');

        $this->addAlternates($blogIndexUrl, $locale, $baseUrl, null, 'blog');

        $sitemap->add($blogIndexUrl);
        $this->info("  Added: /{$locale}/blog");

        // Blog articles
        foreach (config("blog.{$locale}", []) as $slug => $articleData) {
            $blogUrl = Url::create("/{$locale}/blog/{$slug}")
                ->setLastModificationDate($this->parseDate($articleData))
                ->setPriority(0.8)
                ->setChangeFrequency('monthly');

            if (isset($articleData['internal_slug'])) {
                $this->addBlogAlternates($blogUrl, $articleData['internal_slug'], $locale, $baseUrl);
            }

            $sitemap->add($blogUrl);
            $this->info("  Added: /{$locale}/blog/{$slug}");
        }
    }

    /**
     * Build a locale-prefixed path, resolving route translations when applicable.
     */
    private function buildLocalizedPath(string $locale, ?string $routeKey, string $fallbackPath = ''): string
    {
        $segment = $routeKey ? trans($routeKey, [], $locale) : $fallbackPath;

        return $segment !== '' ? "/{$locale}/{$segment}" : "/{$locale}";
    }

    /**
     * Add hreflang alternates for all other locales.
     */
    private function addAlternates(Url $url, string $currentLocale, string $baseUrl, ?string $routeKey = null, string $fallbackPath = ''): void
    {
        foreach (self::LOCALES as $altLocale) {
            if ($altLocale === $currentLocale) {
                continue;
            }

            $altPath = $this->buildLocalizedPath($altLocale, $routeKey, $fallbackPath);
            $url->addAlternate("{$baseUrl}{$altPath}", $altLocale);
        }
    }

    /**
     * Add hreflang alternates for a workout plan matched by internal_type.
     */
    private function addWorkoutPlanAlternates(Url $url, string $internalType, string $currentLocale, string $baseUrl): void
    {
        foreach (self::LOCALES as $altLocale) {
            if ($altLocale === $currentLocale) {
                continue;
            }

            $plans = config("freeWorkouts.{$altLocale}", []);

            foreach ($plans as $slug => $data) {
                if (($data['internal_type'] ?? '') === $internalType) {
                    $basePath = trans('routes.workout_plans_index', [], $altLocale);
                    $url->addAlternate("{$baseUrl}/{$altLocale}/{$basePath}/{$slug}", $altLocale);
                    break;
                }
            }
        }
    }

    /**
     * Add hreflang alternates for a blog article matched by internal_slug.
     */
    private function addBlogAlternates(Url $url, string $internalSlug, string $currentLocale, string $baseUrl): void
    {
        foreach (self::LOCALES as $altLocale) {
            if ($altLocale === $currentLocale) {
                continue;
            }

            foreach (config("blog.{$altLocale}", []) as $altSlug => $altData) {
                if (($altData['internal_slug'] ?? '') === $internalSlug) {
                    $url->addAlternate("{$baseUrl}/{$altLocale}/blog/{$altSlug}", $altLocale);
                    break;
                }
            }
        }
    }

    /**
     * Parse a date from config data, falling back to now.
     */
    private function parseDate(array $data): Carbon
    {
        foreach (['last_updated_at', 'published_at'] as $key) {
            if (isset($data[$key])) {
                return Carbon::parse($data[$key]);
            }
        }

        return Carbon::now();
    }

    private function getLatestPlanUpdate(array $planTypes): Carbon
    {
        $latest = Carbon::now()->subYear();

        foreach ($planTypes as $data) {
            $planDate = $this->parseDate($data);

            if ($planDate->gt($latest)) {
                $latest = $planDate;
            }
        }

        return $latest;
    }
}
