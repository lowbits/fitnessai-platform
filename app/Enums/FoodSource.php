<?php

namespace App\Enums;

enum FoodSource: string
{
    case Custom = 'custom';
    case OpenFoodFacts = 'openfoodfacts';
    case Ai = 'ai';
}
