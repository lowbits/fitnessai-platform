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
        $exercise->loadMissing('translations');

        $this->client
            ->index('exercises')
            ->addDocuments([$exercise->toSearchableArray()]);
    }

    public function deleted(Exercise $exercise): void
    {
        $this->client
            ->index('exercises')
            ->deleteDocument($exercise->id);
    }

    public function restored(Exercise $exercise): void
    {
        $this->saved($exercise);
    }

    public function forceDeleted(Exercise $exercise): void
    {
        $this->deleted($exercise);
    }
}
