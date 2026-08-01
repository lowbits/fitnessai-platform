<?php

use App\Helpers\StructuredData;
use Tests\TestCase;

// Boot the framework (config(), __(), Str) without touching the database.
uses(TestCase::class);

describe('StructuredData::organization', function () {
    it('describes fytrr with an ImageObject logo and language-neutral entity links', function () {
        $org = StructuredData::organization();
        $baseUrl = config('app.url');

        expect($org['@context'])->toBe('https://schema.org')
            ->and($org['@type'])->toBe('Organization')
            ->and($org['name'])->toBe('fytrr')
            ->and($org['url'])->toBe($baseUrl);

        // Logo is a structured ImageObject, not a bare URL string.
        expect($org['logo'])->toMatchArray([
            '@type' => 'ImageObject',
            'url' => "{$baseUrl}/apple-touch-icon.png",
            'width' => 180,
            'height' => 180,
        ]);

        // sameAs anchors the entity across the platforms AI engines cross-check.
        expect($org['sameAs'])->toContain(
            'https://instagram.com/getfytrr',
            'https://apps.apple.com/app/fytrr-ki-personal-trainer/id6757151695',
            'https://www.linkedin.com/company/fytrr',
            'https://www.wikidata.org/wiki/Q140796880',
        );

        expect($org['contactPoint']['availableLanguage'])->toBe(['en', 'de']);
    });

    it('gives the founder a verifiable identity', function () {
        $founder = StructuredData::organization()['founder'];

        expect($founder['@type'])->toBe('Person')
            ->and($founder['name'])->toBe('Tobias Lobitz')
            ->and($founder['jobTitle'])->toBe('Founder & Software Developer')
            ->and($founder['url'])->toEndWith('/en/about')
            ->and($founder['sameAs'])->toContain('https://www.linkedin.com/in/tobiaslobitz/');
    });
});

describe('StructuredData::website', function () {
    it('exposes a bilingual WebSite entity', function () {
        $site = StructuredData::website();

        expect($site['@type'])->toBe('WebSite')
            ->and($site['url'])->toBe(config('app.url'))
            ->and($site['inLanguage'])->toBe(['en', 'de'])
            ->and($site['publisher']['@type'])->toBe('Organization');
    });
});

describe('StructuredData::breadcrumbs', function () {
    it('returns null on a locale root (nothing beyond Home)', function () {
        expect(StructuredData::breadcrumbs('en', 'en'))->toBeNull();
    });

    it('builds a positioned trail and strips the locale prefix', function () {
        $crumbs = StructuredData::breadcrumbs('en', 'en/free-workout-plan/weight-loss');
        $baseUrl = config('app.url');

        expect($crumbs['@type'])->toBe('BreadcrumbList');

        $items = $crumbs['itemListElement'];
        expect($items)->toHaveCount(3);

        expect($items[0])->toMatchArray([
            'position' => 1,
            'name' => 'Home',
            'item' => "{$baseUrl}/en",
        ]);
        expect($items[1])->toMatchArray([
            'position' => 2,
            'name' => 'Free workout plan',
            'item' => "{$baseUrl}/en/free-workout-plan",
        ]);
        expect($items[2])->toMatchArray([
            'position' => 3,
            'name' => 'Weight loss',
            'item' => "{$baseUrl}/en/free-workout-plan/weight-loss",
        ]);
    });

    it('prefers the page title for the final crumb', function () {
        $crumbs = StructuredData::breadcrumbs('en', 'en/blog/some-article', [
            'meta' => ['title' => 'How to Create a Meal Plan | fytrr'],
        ]);

        expect(end($crumbs['itemListElement'])['name'])->toBe('How to Create a Meal Plan');
    });

    it('falls back to the article headline when there is no title', function () {
        $crumbs = StructuredData::breadcrumbs('en', 'en/blog/some-article', [
            'article' => ['h1' => 'Calorie Needs Explained'],
        ]);

        expect(end($crumbs['itemListElement'])['name'])->toBe('Calorie Needs Explained');
    });
});

describe('StructuredData::forRequest', function () {
    it('emits Organization + WebSite on a locale root, adding BreadcrumbList on deeper pages', function () {
        $root = StructuredData::forRequest('en', 'en');
        expect($root)->toHaveCount(2)
            ->and(array_column($root, '@type'))->toBe(['Organization', 'WebSite']);

        $deep = StructuredData::forRequest('en', 'en/blog/some-article');
        expect($deep)->toHaveCount(3)
            ->and(array_column($deep, '@type'))->toBe(['Organization', 'WebSite', 'BreadcrumbList']);
    });

    it('produces valid, encodable JSON for every graph', function () {
        foreach (StructuredData::forRequest('en', 'en/free-workout-plan/weight-loss') as $graph) {
            $json = json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            expect(json_last_error())->toBe(JSON_ERROR_NONE)
                ->and($json)->toContain('"@type"');
        }
    });
});
