<?php

namespace App\Enums;

/**
 * The visual/structural format of a meal — how it shows up on the plate.
 *
 * Used together with primary_protein and hero_veg to detect template-twins:
 * two meals matching on all three axes are effectively the same dish with a
 * different name (e.g. Udon-Tofu-Erdnuss vs Soba-Tofu-Erdnuss). The variety
 * planner blocks that case.
 */
enum MealFormat: string
{
    case BOWL = 'bowl';
    case PASTA = 'pasta';
    case NOODLES = 'noodles';
    case WRAP = 'wrap';
    case SANDWICH = 'sandwich';
    case SOUP = 'soup';
    case SALAD = 'salad';
    case STIR_FRY = 'stir_fry';
    case CURRY = 'curry';
    case BAKE = 'bake';
    case GRILL = 'grill';
    case SHEET_PAN = 'sheet_pan';
    case OMELET = 'omelet';
    case PORRIDGE = 'porridge';
    case PANCAKE = 'pancake';
    case TOAST = 'toast';
    case YOGURT_BOWL = 'yogurt_bowl';
    case SMOOTHIE = 'smoothie';
    case PIZZA = 'pizza';
    case MIXED = 'mixed';
}
