<?php

namespace App\Jobs;

use App\Ai\Agents\ExerciseTranslationAgent;
use App\Models\Exercise;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EnrichExerciseTranslationsJob implements ShouldQueue
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
        public Exercise $exercise,
        public string $locale,
    ) {
        $this->onQueue('exercises');
    }

    public function handle(): void
    {
        if ($this->exercise->deleted_at !== null) {
            Log::info('[EnrichExerciseTranslations] Skipping soft-deleted exercise', [
                'exercise_id' => $this->exercise->id,
            ]);

            return;
        }

        if ($this->exercise->description === null) {
            Log::warning('[EnrichExerciseTranslations] Exercise not yet enriched, skipping translation', [
                'exercise_id' => $this->exercise->id,
                'locale' => $this->locale,
            ]);

            return;
        }

        Log::info('[EnrichExerciseTranslations] Starting translation', [
            'exercise_id' => $this->exercise->id,
            'exercise_name' => $this->exercise->name,
            'locale' => $this->locale,
        ]);

        $agent = new ExerciseTranslationAgent($this->exercise, $this->locale);
        $response = $agent->prompt($agent->buildPrompt());

        $translationData = [
            'name' => $response['name'],
            'aliases' => $response['aliases'],
            'description' => $response['description'],
            'instructions' => $response['instructions'],
            'form_cues' => $response['form_cues'],
        ];

        $this->exercise->translations()->updateOrCreate(
            ['locale' => $this->locale],
            $translationData,
        );

        Log::info('[EnrichExerciseTranslations] Completed', [
            'exercise_id' => $this->exercise->id,
            'locale' => $this->locale,
        ]);
    }
}
