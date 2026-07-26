<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::OpenAI)]
#[UseCheapestModel]
#[MaxTokens(100)]
#[Temperature(0.8)]
class RecipeTaglineAgent implements Agent
{
    use Promptable;

    public function __construct(
        private readonly string $locale = 'en',
    ) {}

    public function instructions(): string
    {
        $language = $this->locale === 'de' ? 'German' : 'English';

        return <<<INSTRUCTIONS
        You generate short, fun, one-line taglines for recipes shown in a food swiping screen (like Tinder for meals).

        Rules:
        - Max 8 words
        - Casual, witty, appetizing tone
        - No emojis
        - No quotes
        - Write in {$language}
        - Return ONLY the tagline, nothing else
        INSTRUCTIONS;
    }
}
