<?php

use App\Models\FoodTranslation;
use App\Services\Recipe\FoodTermTranslator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('resolves a common term from the free static map without touching the DB', function () {
    $english = app(FoodTermTranslator::class)->toEnglish('Apfel');

    expect($english)->toBe('apple')
        ->and(FoodTranslation::count())->toBe(0);
});

test('resolves a learned term from the dictionary', function () {
    FoodTranslation::create(['term' => 'pomme', 'translation' => 'apple']);

    expect(app(FoodTermTranslator::class)->toEnglish('Pomme'))->toBe('apple');
});

test('translateMany normalizes, dedupes and translates', function () {
    FoodTranslation::create(['term' => 'pomme', 'translation' => 'apple']);

    $result = app(FoodTermTranslator::class)->toEnglishMany(['Apfel', 'pomme', 'Banane']);

    expect($result)->toBe(['apple', 'banana']);
});
