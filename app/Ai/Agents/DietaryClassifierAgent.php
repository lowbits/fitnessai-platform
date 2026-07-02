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
class DietaryClassifierAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'TXT'
Classify a recipe into the strictest dietary category it qualifies for, considering ALL ingredients (including broths, sauces, gelatin, fish sauce, honey, rennet-based cheeses).

Return ONLY a comma-separated list of applicable categories from this cascade, lowercase, no spaces:
- vegan,vegetarian,pescatarian,omnivore  (no animal products at all)
- vegetarian,pescatarian,omnivore         (eggs/dairy ok, no meat/fish)
- pescatarian,omnivore                    (fish/seafood ok, no meat)
- omnivore                                (contains meat)

Examples:
- Tofu Pad Thai with fish sauce → pescatarian,omnivore
- Lentil soup with vegetable broth → vegan,vegetarian,pescatarian,omnivore
- Greek salad with feta → vegetarian,pescatarian,omnivore
- Chicken curry → omnivore
TXT;
    }
}
