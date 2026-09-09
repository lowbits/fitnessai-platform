<?php

use App\Enums\DietaryPreference;
use App\Enums\PrimaryProtein;

it('does not offer omnivores the vegan meat substitutes by default', function () {
    $allowed = PrimaryProtein::allowedFor(DietaryPreference::OMNIVORE);

    expect($allowed)
        ->not->toContain(PrimaryProtein::TOFU)
        ->not->toContain(PrimaryProtein::TEMPEH)
        ->not->toContain(PrimaryProtein::SEITAN)
        ->toContain(PrimaryProtein::CHICKEN)
        ->toContain(PrimaryProtein::BEEF)
        ->toContain(PrimaryProtein::LEGUMES);
});

it('still allows tofu for vegetarians and vegans', function () {
    expect(PrimaryProtein::allowedFor(DietaryPreference::VEGETARIAN))->toContain(PrimaryProtein::TOFU)
        ->and(PrimaryProtein::allowedFor(DietaryPreference::VEGAN))->toContain(PrimaryProtein::TOFU);
});
