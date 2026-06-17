<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

#[UseCheapestModel]
#[MaxTokens(50)]
#[Temperature(0)]
class FoodTranslatorAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return 'Translate the given food or ingredient name to English. Return ONLY the single English word, lowercase, nothing else. If already English, return as-is.';
    }
}
