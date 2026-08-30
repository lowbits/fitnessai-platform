<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetUserLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            SecurityHeaders::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            SetUserLocale::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(append: [
            SetUserLocale::class,
        ]);

        $middleware->alias([
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localeCookieRedirect' => LocaleCookieRedirect::class,
            'localeViewPath' => LaravelLocalizationViewPath::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->respond(function (SymfonyResponse $response, Throwable $exception, Request $request) {
            if (app()->environment('testing') || $request->expectsJson()) {
                return $response;
            }

            $status = $response->getStatusCode();

            if (! in_array($status, [403, 404, 500, 503], true)) {
                return $response;
            }

            if (config('app.debug') && in_array($status, [500, 503], true)) {
                return $response;
            }

            $segment = explode('/', trim($request->path(), '/'))[0] ?? '';
            $locale = in_array($segment, ['de', 'en'], true) ? $segment : config('app.locale');

            $copy = trans("errors.statuses.{$status}", [], $locale);
            if (! is_array($copy)) {
                $copy = trans('errors.default', [], $locale);
            }

            return Inertia::render('Error', [
                'status' => $status,
                'title' => $copy['title'],
                'message' => $copy['message'],
                'labels' => [
                    'home' => trans('errors.home', [], $locale),
                    'app' => trans('errors.app', [], $locale),
                    'appPitch' => trans('errors.app_pitch', [], $locale),
                    'quicklinks' => trans('errors.quicklinks', [], $locale),
                    'calorie' => trans('errors.calorie', [], $locale),
                    'calorieDesc' => trans('errors.calorie_desc', [], $locale),
                    'workout' => trans('errors.workout', [], $locale),
                    'workoutDesc' => trans('errors.workout_desc', [], $locale),
                ],
                'links' => [
                    'home' => "/{$locale}",
                    'calorie' => "/{$locale}/".trans('routes.free_tools_calorie_calculator', [], $locale),
                    'workout' => "/{$locale}/".trans('routes.workout_plans_index', [], $locale),
                    'appStore' => config('app.app_store.ios.url'),
                    'badge' => '/assets/badges/App_Store_Badge_'.strtoupper($locale).'.svg',
                ],
            ])->toResponse($request)->setStatusCode($status);
        });
    })->create();
