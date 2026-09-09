<?php

use App\Services\Recipe\DislikeFilter;
use App\Services\Recipe\FoodTermTranslator;

function dislikeClauses(array $dislikes): array
{
    return (new DislikeFilter(new FoodTermTranslator))->clauses($dislikes);
}

it('excludes tofu and tempeh when soy is disliked', function () {
    $clauses = dislikeClauses(['soy']);

    expect($clauses)
        ->toContain('primary_protein != "tofu"')
        ->toContain('primary_protein != "tempeh"')
        ->toContain('allergens != "soy"')
        ->toContain('ingredient_names != "edamame"');
});

it('excludes peanuts when nuts is disliked', function () {
    $clauses = dislikeClauses(['nuts']);

    expect($clauses)
        ->toContain('allergens != "nuts"')
        ->toContain('allergens != "peanuts"');
});

it('maps EU allergen keys to their allergen filter', function () {
    $clauses = dislikeClauses(['crustaceans', 'molluscs']);

    expect($clauses)
        ->toContain('allergens != "crustaceans"')
        ->toContain('allergens != "molluscs"');
});

it('maps a concrete member term back to its allergen category', function () {
    expect(dislikeClauses(['shrimp']))->toContain('allergens != "crustaceans"')
        ->and(dislikeClauses(['mussels']))->toContain('allergens != "molluscs"');
});

it('translates a German dislike before matching', function () {
    $clauses = dislikeClauses(['soja']);

    expect($clauses)->toContain('primary_protein != "tofu"');
});

it('ignores blank entries', function () {
    expect(dislikeClauses(['', '  ']))->toBe([]);
});
