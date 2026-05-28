<?php

namespace App\Helpers;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LocalizationHelper
{
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

        foreach (array_keys(LaravelLocalization::getSupportedLocales()) as $locale) {
            $url = match ($routeName) {
                'workout-plan.show' => static::workoutPlanShowUrl($route->parameter('type'), $locale),
                'blog.show' => static::blogShowUrl($route->parameter('slug'), $locale),
                'landing.free-workout-meal-plan' => static::landingPagePairUrl($locale, 'de', 'landing_personal_meal_plan'),
                'landing.personal-meal-plan' => static::landingPagePairUrl($locale, 'en', 'landing_free_workout_meal_plan'),
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
     * Resolve a hreflang URL for a landing page paired across locales.
     */
    private static function landingPagePairUrl(string $targetLocale, string $pairedLocale, string $pairedRouteKey): ?string
    {
        if ($targetLocale !== $pairedLocale) {
            return null;
        }

        $path = trans("routes.{$pairedRouteKey}", [], $pairedLocale);

        return LaravelLocalization::localizeURL("/{$path}", $pairedLocale);
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
