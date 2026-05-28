<?php

namespace App\Helpers;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LocalizationHelper
{
    /**
     * Landing pages: which locale they exist in, and their cross-locale hreflang pair (if any).
     * Key = route name, value = [locale => route_key, ...] mapping for each valid locale.
     *
     * @var array<string, array<string, string>>
     */
    private const LANDING_PAGE_ALTERNATES = [
        'landing.free-workout-meal-plan' => [
            'en' => 'landing_free_workout_meal_plan',
            'de' => 'landing_personal_meal_plan',     // cross-locale pair
        ],
        'landing.personal-meal-plan' => [
            'de' => 'landing_personal_meal_plan',
            'en' => 'landing_free_workout_meal_plan',  // cross-locale pair
        ],
        'landing.ai-workout-plan-generator' => [
            'en' => 'landing_ai_workout_plan_generator',
            // no DE version
        ],
    ];

    /**
     * Generate alternate URLs for the current route across all supported locales.
     * Handles translated route parameters (slugs) that getLocalizedURL() cannot resolve.
     *
     * @return array<string, string>
     */
    public static function getAlternateUrls(): array
    {
        $route = request()->route();
        $routeName = $route?->getName();
        $urls = [];

        // Landing pages: use explicit alternate mapping
        if (isset(self::LANDING_PAGE_ALTERNATES[$routeName])) {
            foreach (self::LANDING_PAGE_ALTERNATES[$routeName] as $locale => $routeKey) {
                $path = trans("routes.{$routeKey}", [], $locale);
                $urls[$locale] = LaravelLocalization::localizeURL("/{$path}", $locale);
            }

            return $urls;
        }

        foreach (array_keys(LaravelLocalization::getSupportedLocales()) as $locale) {
            $url = match ($routeName) {
                'workout-plan.show' => static::workoutPlanShowUrl($route->parameter('type'), $locale),
                'blog.show' => static::blogShowUrl($route->parameter('slug'), $locale),
                default => LaravelLocalization::getLocalizedURL($locale, null, [], true),
            };

            if ($url) {
                $urls[$locale] = $url;
            }
        }

        return $urls;
    }

    /**
     * Translate a workout plan show URL to the given locale.
     */
    private static function workoutPlanShowUrl(string $currentSlug, string $targetLocale): ?string
    {
        $currentLocale = app()->getLocale();
        $types = trans('routes.type', [], $currentLocale);

        $internalType = array_search($currentSlug, $types);
        if ($internalType === false) {
            return null;
        }

        $translatedSlug = trans("routes.type.{$internalType}", [], $targetLocale);
        $basePath = trans('routes.workout_plans_index', [], $targetLocale);

        return LaravelLocalization::localizeURL("/{$basePath}/{$translatedSlug}", $targetLocale);
    }

    /**
     * Translate a blog show URL to the given locale.
     */
    private static function blogShowUrl(string $currentSlug, string $targetLocale): ?string
    {
        $currentLocale = app()->getLocale();
        $currentArticles = config("blog.{$currentLocale}", []);
        $internalSlug = $currentArticles[$currentSlug]['internal_slug'] ?? null;

        if (! $internalSlug) {
            return null;
        }

        $targetArticles = config("blog.{$targetLocale}", []);
        foreach ($targetArticles as $slug => $data) {
            if (($data['internal_slug'] ?? '') === $internalSlug) {
                return LaravelLocalization::localizeURL("/blog/{$slug}", $targetLocale);
            }
        }

        return null;
    }
}
