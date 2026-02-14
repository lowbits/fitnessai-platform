<?php

namespace App\Observers;

use App\Models\Exercise;
use Meilisearch\Client;

class ExerciseObserver
{
    public function __construct(private readonly Client $client)
    {
    }


    public function saved(Exercise $exercise): void
    {
        if (app()->environment('testing')) return;
        $exercise->loadMissing('translations');

        $this->client
            ->index('exercises')
            ->addDocuments([$exercise->toSearchableArray()]);
    }

    public function deleted(Exercise $exercise): void
    {
        if (app()->environment('testing')) return;
        $this->client
            ->index('exercises')
            ->deleteDocument($exercise->id);
    }

    public function restored(Exercise $exercise): void
    {
        if (app()->environment('testing')) return;
        $this->saved($exercise);
    }

    public function forceDeleted(Exercise $exercise): void
    {
        if (app()->environment('testing')) return;
        $this->deleted($exercise);
    }
}
