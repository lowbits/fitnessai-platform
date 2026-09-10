<?php

use App\Http\Controllers\GlossaryController;
use Illuminate\Http\Request;

// The mcamara locale route group does not register under the Pest kernel, so the
// controller is exercised directly through its Inertia response, the same path a
// real request takes minus routing (mirrors CalorieCalculatorTest).
function renderGlossary(string $locale = 'de'): array
{
    app()->setLocale($locale);
    $request = Request::create("/{$locale}/fitness-glossar", 'GET');
    $request->headers->set('X-Inertia', 'true');

    return app(GlossaryController::class)->index()->toResponse($request)->getData(true);
}

it('renders the glossary component with all content props', function () {
    $page = renderGlossary('de');

    expect($page['component'])->toBe('Glossary/Index')
        ->and($page['props'])->toHaveKeys(['meta', 'schema', 'hero', 'ui', 'categories', 'terms', 'faqs', 'cta', 'internalLinks'])
        ->and($page['props']['terms'])->not->toBeEmpty()
        ->and($page['props']['meta']['canonical'])->toContain('/de/fitness-glossar');
});

it('exposes a DefinedTermSet and FAQPage schema', function () {
    $types = array_column(renderGlossary('de')['props']['schema'], '@type');

    expect($types)->toContain('DefinedTermSet')->toContain('FAQPage');
});

it('gives every term a slug, term, definition and known category', function () {
    $page = renderGlossary('de');
    $categoryIds = array_column($page['props']['categories'], 'id');

    foreach ($page['props']['terms'] as $term) {
        expect($term)->toHaveKeys(['slug', 'term', 'definition', 'category'])
            ->and($term['slug'])->not->toBeEmpty()
            ->and($term['definition'])->not->toBeEmpty()
            ->and($categoryIds)->toContain($term['category']);
    }
});

it('includes the core plan-notation terms users actually see', function () {
    $slugs = array_column(renderGlossary('de')['props']['terms'], 'slug');

    expect($slugs)->toContain('rpe', 'tempo', 'tdee', 'progressive-overload');
});

it('translates the terms for the english locale', function () {
    $page = renderGlossary('en');

    expect($page['props']['meta']['canonical'])->toContain('/en/fitness-glossary')
        ->and($page['props']['terms'])->not->toBeEmpty();
});
