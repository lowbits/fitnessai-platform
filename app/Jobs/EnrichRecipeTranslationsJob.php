<?php

namespace App\Jobs;

use App\Ai\Agents\RecipeTranslationAgent;
use App\Models\Recipe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EnrichRecipeTranslationsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * @return array<int>
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public function __construct(
        public Recipe $recipe,
        public string $locale,
    ) {
        $this->onQueue('content');
    }

    public function handle(): void
    {
        if ($this->recipe->deleted_at !== null) {
            Log::info('[EnrichRecipeTranslations] Skipping soft-deleted recipe', [
                'recipe_id' => $this->recipe->id,
            ]);

            return;
        }

        Log::info('[EnrichRecipeTranslations] Starting translation', [
            'recipe_id' => $this->recipe->id,
            'recipe_name' => $this->recipe->name,
            'locale' => $this->locale,
        ]);

        $agent = new RecipeTranslationAgent($this->recipe, $this->locale);
        $response = $agent->prompt($agent->buildPrompt());

        $this->recipe->translations()->updateOrCreate(
            ['locale' => $this->locale],
            [
                'name' => $response['name'],
                'slug' => Str::slug($response['name']),
                'aliases' => $response['aliases'],
                'description' => $response['description'],
                'instructions' => $response['instructions'],
                'ingredients' => $response['ingredients'],
            ],
        );

        $this->recipe->update(['needs_translation' => false]);

        Log::info('[EnrichRecipeTranslations] Completed', [
            'recipe_id' => $this->recipe->id,
            'locale' => $this->locale,
        ]);
    }
}
