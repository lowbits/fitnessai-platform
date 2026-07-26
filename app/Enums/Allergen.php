<?php

namespace App\Enums;

/**
 * The 14 EU-standard food allergens. The canonical values are what we store;
 * the AI's allergen output is locked to this list via the SaveMealPlanTool
 * schema, so we never see "Milchprodukte" / "Krustentiere" / "Glutenfrei
 * möglich" leak in as allergen entries again.
 */
enum Allergen: string
{
    case GLUTEN = 'gluten';
    case CRUSTACEANS = 'crustaceans';
    case EGGS = 'eggs';
    case FISH = 'fish';
    case PEANUTS = 'peanuts';
    case SOY = 'soy';
    case DAIRY = 'dairy';
    case NUTS = 'nuts';
    case CELERY = 'celery';
    case MUSTARD = 'mustard';
    case SESAME = 'sesame';
    case SULPHITES = 'sulphites';
    case LUPIN = 'lupin';
    case MOLLUSCS = 'molluscs';
}
