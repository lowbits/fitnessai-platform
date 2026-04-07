<?php

use Illuminate\Support\Facades\File;

afterEach(function () {
    $path = public_path('sitemap.xml');
    if (File::exists($path)) {
        File::delete($path);
    }
});

it('generates sitemap successfully', function () {
    $this->artisan('generate:sitemap')
        ->assertSuccessful();

    expect(File::exists(public_path('sitemap.xml')))->toBeTrue();
});

it('includes all static pages for both locales', function () {
    $this->artisan('generate:sitemap')->assertSuccessful();

    $sitemap = File::get(public_path('sitemap.xml'));

    // English static pages
    expect($sitemap)
        ->toContain('/en')
        ->toContain('/en/app')
        ->toContain('/en/about')
        ->toContain('/en/free-tools/calorie-calculator')
        ->toContain('/en/imprint')
        ->toContain('/en/privacy-policy')
        ->toContain('/en/terms-and-conditions')
        ->toContain('/en/disclaimer');

    // German static pages
    expect($sitemap)
        ->toContain('/de')
        ->toContain('/de/app')
        ->toContain('/de/ueber-uns')
        ->toContain('/de/kostenlose-tools/kalorienrechner')
        ->toContain('/de/impressum')
        ->toContain('/de/datenschutz')
        ->toContain('/de/agb')
        ->toContain('/de/haftungsausschluss');
});

it('includes blog index for both locales', function () {
    $this->artisan('generate:sitemap')->assertSuccessful();

    $sitemap = File::get(public_path('sitemap.xml'));

    expect($sitemap)
        ->toContain('/en/blog')
        ->toContain('/de/blog');
});

it('includes workout plan index for both locales', function () {
    $this->artisan('generate:sitemap')->assertSuccessful();

    $sitemap = File::get(public_path('sitemap.xml'));

    expect($sitemap)
        ->toContain('/en/free-workout-plan')
        ->toContain('/de/kostenloser-trainingsplan');
});

it('includes hreflang alternates for static pages', function () {
    $this->artisan('generate:sitemap')->assertSuccessful();

    $xml = simplexml_load_file(public_path('sitemap.xml'));
    $namespaces = $xml->getNamespaces(true);

    $hasAlternates = false;

    foreach ($xml->url as $url) {
        $xhtml = $url->children($namespaces['xhtml'] ?? '');
        if (count($xhtml) > 0) {
            $hasAlternates = true;
            break;
        }
    }

    expect($hasAlternates)->toBeTrue();
});
