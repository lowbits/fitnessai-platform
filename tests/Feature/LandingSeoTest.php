<?php

use Illuminate\Support\Facades\Route;

// These assert the config-driven SEO content the KI-Ernährungsplan work added.
// They intentionally check config (not an HTTP round-trip): the landing routes
// are localized, so the reachable slug depends on the boot locale, which differs
// between local (de) and CI (en). Config is deterministic everywhere.

it('registers the localized meal-plan landing routes', function () {
    expect(Route::has('landing.personal-meal-plan'))->toBeTrue()
        ->and(Route::has('landing.free-workout-meal-plan'))->toBeTrue()
        ->and(Route::has('workout-plan.index'))->toBeTrue();
});

it('carries the KI-Trainingsplan phrase and KI-Ernährungsplan cross-link in the workout hub labels', function () {
    $labels = config('freeWorkouts.index_labels.de');

    expect($labels['meta_title'])->toContain('KI-Trainingsplan')
        ->and($labels['heading'])->toContain('KI-Trainingsplan')
        ->and($labels['crossLinkLabel'])->toContain('KI-Ernährungsplan')
        ->and($labels['crossLinkUrl'])->toBe('/de/persoenlicher-ernaehrungsplan');
});

it('links high in the ernaehrungsplan-erstellen article to the KI-Ernährungsplan tool page', function () {
    $cta = config('blog.de.ernaehrungsplan-erstellen.sections.0.cta');

    expect($cta['url'])->toBe('/de/persoenlicher-ernaehrungsplan')
        ->and($cta['label'])->toContain('KI-Ernährungsplan');
});
