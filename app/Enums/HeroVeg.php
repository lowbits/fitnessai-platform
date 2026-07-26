<?php

namespace App\Enums;

/**
 * The dominant non-starch vegetable in a meal.
 *
 * Used as a variety axis. Note: starches (potato, sweet potato, rice, pasta)
 * are *not* hero_veg — they're carb bases. A "potato bake with broccoli"
 * has hero_veg=BROCCOLI; broccoli is the variety signal, the potato is
 * filler.
 *
 * If a meal has no recognizable vegetable, hero_veg is NONE.
 * If it has multiple equal-weight vegetables and no clear hero, MIXED.
 */
enum HeroVeg: string
{
    case BROCCOLI = 'broccoli';
    case SPINACH = 'spinach';
    case CAULIFLOWER = 'cauliflower';
    case BELL_PEPPER = 'bell_pepper';
    case TOMATO = 'tomato';
    case MUSHROOM = 'mushroom';
    case ZUCCHINI = 'zucchini';
    case EGGPLANT = 'eggplant';
    case KALE = 'kale';
    case CABBAGE = 'cabbage';
    case CARROT = 'carrot';
    case GREEN_BEANS = 'green_beans';
    case ASPARAGUS = 'asparagus';
    case CORN = 'corn';
    case PEAS = 'peas';
    case CUCUMBER = 'cucumber';
    case LETTUCE = 'lettuce';
    case AVOCADO = 'avocado';
    case ONION = 'onion';
    case LEEK = 'leek';
    case BRUSSELS_SPROUTS = 'brussels_sprouts';
    case ARTICHOKE = 'artichoke';
    case FENNEL = 'fennel';
    case CELERY = 'celery';
    case BEETROOT = 'beetroot';
    case PUMPKIN = 'pumpkin';
    case MIXED = 'mixed';
    case NONE = 'none';
}
