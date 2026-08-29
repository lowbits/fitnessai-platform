<?php

use App\Enums\MealType;
use App\Support\FlexShake;

it('builds a shake that hits the target kcal with protein-forward macros', function () {
    $shake = FlexShake::build(296, 'de');

    expect($shake['type'])->toBe(MealType::FLEX->value)
        ->and($shake['recipe_id'])->toBeNull()
        ->and($shake['calories'])->toBe(296)
        ->and($shake['protein_g'])->toBe(26)   // 296 * 0.35 / 4
        ->and($shake['carbs_g'])->toBe(30)     // 296 * 0.40 / 4
        ->and($shake['fat_g'])->toBe(8);       // 296 * 0.25 / 9
});

it('scales the ingredient amounts with the kcal', function () {
    $small = collect(FlexShake::build(175, 'de')['ingredients'])->firstWhere('name', 'Milch');
    $large = collect(FlexShake::build(700, 'de')['ingredients'])->firstWhere('name', 'Milch');

    expect((int) $small['amount'])->toBeLessThan((int) $large['amount']);
});

it('localizes copy but keeps enum fields canonical', function () {
    $de = FlexShake::build(300, 'de');
    $en = FlexShake::build(300, 'en');

    expect($de['name'])->toContain('Proteinshake')
        ->and($en['name'])->toContain('Protein shake')
        ->and($de['format'])->toBe('smoothie')
        ->and($de['primary_protein'])->toBe('dairy')
        ->and($de['allergens'])->toBe(['dairy', 'peanuts', 'gluten']);
});
