<?php

namespace App\Observers;

use App\Models\Recipe;
use Meilisearch\Client;

class RecipeObserver
{
    public function __construct(private readonly Client $client) {}

    public function saved(Recipe $recipe): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $recipe->loadMissing('translations');

        $this->client
            ->index('recipes')
            ->addDocuments([$recipe->toSearchableArray()]);
    }

    public function deleted(Recipe $recipe): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->client
            ->index('recipes')
            ->deleteDocument($recipe->id);
    }

    public function restored(Recipe $recipe): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->saved($recipe);
    }

    public function forceDeleted(Recipe $recipe): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->deleted($recipe);
    }
}
