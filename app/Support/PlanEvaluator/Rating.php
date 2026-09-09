<?php

namespace App\Support\PlanEvaluator;

enum Rating: string
{
    case Good = 'good';
    case Ok = 'ok';
    case Poor = 'poor';

    public function points(): float
    {
        return match ($this) {
            self::Good => 1.0,
            self::Ok => 0.5,
            self::Poor => 0.0,
        };
    }
}
