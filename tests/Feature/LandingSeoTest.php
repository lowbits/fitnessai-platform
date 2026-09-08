<?php

use Inertia\Testing\AssertableInertia as Assert;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

beforeEach(function () {
    // In the test/CLI context mcamara registers the localized routes at the
    // root path; its redirect middleware otherwise bounces to the locale home.
    $this->withoutMiddleware([
        LaravelLocalizationRedirectFilter::class,
        LocaleSessionRedirect::class,
        LocaleCookieRedirect::class,
    ]);
});

it('renders the personal meal plan landing page', function () {
    $this->get('/persoenlicher-ernaehrungsplan')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Landing/PersonalMealPlan')
        );
});

it('surfaces the KI-Trainingsplan phrase and KI-Ernährungsplan cross-link on the workout hub', function () {
    $this->get('/kostenloser-trainingsplan')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('WorkoutPlan/Index')
            ->where('meta.title', fn ($title) => str_contains($title, 'KI-Trainingsplan'))
            ->where('labels.heading', fn ($heading) => str_contains($heading, 'KI-Trainingsplan'))
            ->where('labels.crossLinkLabel', fn ($label) => str_contains($label, 'KI-Ernährungsplan'))
            ->where('labels.crossLinkUrl', '/de/persoenlicher-ernaehrungsplan')
        );
});

it('links high in the ernaehrungsplan-erstellen article to the KI-Ernährungsplan tool page', function () {
    $this->get('/blog/ernaehrungsplan-erstellen')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog/Show')
            ->where('article.sections.0.cta.url', '/de/persoenlicher-ernaehrungsplan')
            ->where('article.sections.0.cta.label', fn ($label) => str_contains($label, 'KI-Ernährungsplan'))
        );
});
