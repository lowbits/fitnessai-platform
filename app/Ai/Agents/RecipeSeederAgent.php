<?php

namespace App\Ai\Agents;

use App\Ai\Prompts\SeedRecipesPrompt;
use App\Ai\Tools\SaveRecipesTool;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Timeout(300)]
#[Provider([Lab::OpenAI])]
class RecipeSeederAgent implements Agent, HasTools
{
    use Promptable;

    public SaveRecipesTool $saveTool;

    public function __construct(
        private readonly string $diet,
        private readonly string $mealType,
        private readonly string $locale,
        private readonly int $count,
        bool $dryRun = false,
        private readonly ?string $request = null,
        private readonly ?int $targetKcal = null,
    ) {
        $this->saveTool = new SaveRecipesTool($this->locale, $this->mealType, $dryRun);
    }

    public function model(): string
    {
        return config('ai.models.agent');
    }

    public function instructions(): string
    {
        return (string) new SeedRecipesPrompt($this->diet, $this->mealType, $this->locale, $this->count, $this->request, $this->targetKcal);
    }

    public function tools(): iterable
    {
        return [$this->saveTool];
    }
}
