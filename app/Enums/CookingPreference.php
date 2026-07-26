<?php

namespace App\Enums;

enum CookingPreference: string
{
    case QUICK = 'quick';
    case NORMAL = 'normal';
    case ELABORATE = 'elaborate';

    public function label(): string
    {
        return __('enums.cookingPreference.'.$this->value);
    }

    public function maxMinutes(): int
    {
        return match ($this) {
            self::QUICK => 15,
            self::NORMAL => 30,
            self::ELABORATE => 60,
        };
    }
}
