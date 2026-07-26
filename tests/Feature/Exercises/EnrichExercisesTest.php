<?php

use App\Ai\Agents\ExerciseEnrichmentAgent;
use App\Ai\Agents\ExerciseTranslationAgent;
use App\Jobs\EnrichExerciseJob;
use App\Jobs\EnrichExerciseTranslationsJob;
use App\Models\Exercise;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
});

describe('exercises:enrich command', function () {
    it('dispatches enrichment and translation jobs for exercises missing data', function () {
        Exercise::factory()->create([
            'description' => null,
            'movement_pattern' => null,
        ]);

        $this->artisan('exercises:enrich')
            ->assertSuccessful()
            ->expectsOutputToContain('1 enrichment jobs and 2 translation jobs');

        Queue::assertPushedOn('content',EnrichExerciseJob::class);
        Queue::assertPushedOn('content',EnrichExerciseTranslationsJob::class);
    });

    it('skips exercises that are already enriched without --force', function () {
        Exercise::factory()->create([
            'description' => 'Already enriched',
            'movement_pattern' => 'squat',
        ]);

        $this->artisan('exercises:enrich')
            ->assertSuccessful()
            ->expectsOutputToContain('No exercises found');

        Queue::assertNothingPushed();
    });

    it('re-enriches with --force flag', function () {
        Exercise::factory()->create([
            'description' => 'Already enriched',
            'movement_pattern' => 'squat',
        ]);

        $this->artisan('exercises:enrich --force')
            ->assertSuccessful()
            ->expectsOutputToContain('1 enrichment jobs');

        Queue::assertPushedOn('content',EnrichExerciseJob::class);
    });

    it('filters by --ids option', function () {
        $exercise1 = Exercise::factory()->create(['description' => null, 'movement_pattern' => null]);
        Exercise::factory()->create(['description' => null, 'movement_pattern' => null]);

        $this->artisan("exercises:enrich --ids={$exercise1->id}")
            ->assertSuccessful();

        Queue::assertPushed(EnrichExerciseJob::class, 1);
    });

    it('respects --limit option', function () {
        Exercise::factory()->count(5)->create(['description' => null, 'movement_pattern' => null]);

        $this->artisan('exercises:enrich --limit=2')
            ->assertSuccessful();

        Queue::assertPushed(EnrichExerciseJob::class, 2);
    });

    it('shows dry-run without dispatching', function () {
        Exercise::factory()->create(['description' => null, 'movement_pattern' => null, 'name' => 'Bench Press']);

        $this->artisan('exercises:enrich --dry-run')
            ->assertSuccessful()
            ->expectsOutputToContain('Dry run')
            ->expectsOutputToContain('Bench Press');

        Queue::assertNothingPushed();
    });

    it('skips enrichment with --skip-enrichment', function () {
        Exercise::factory()->create(['description' => null, 'movement_pattern' => null]);

        $this->artisan('exercises:enrich --skip-enrichment --force')
            ->assertSuccessful()
            ->expectsOutputToContain('0 enrichment jobs and 2 translation jobs');

        Queue::assertNotPushed(EnrichExerciseJob::class);
        Queue::assertPushed(EnrichExerciseTranslationsJob::class, 2);
    });

    it('skips translations with --skip-translations', function () {
        Exercise::factory()->create(['description' => null, 'movement_pattern' => null]);

        $this->artisan('exercises:enrich --skip-translations')
            ->assertSuccessful()
            ->expectsOutputToContain('0 translation jobs');

        Queue::assertNotPushed(EnrichExerciseTranslationsJob::class);
    });

    it('uses custom locales', function () {
        Exercise::factory()->create(['description' => null, 'movement_pattern' => null]);

        $this->artisan('exercises:enrich --locales=de,en,es')
            ->assertSuccessful()
            ->expectsOutputToContain('3 translation jobs');

        Queue::assertPushed(EnrichExerciseTranslationsJob::class, 3);
    });
});

describe('EnrichExerciseJob', function () {
    it('enriches null fields via AI agent', function () {
        ExerciseEnrichmentAgent::fake();

        $exercise = Exercise::factory()->create([
            'name' => 'Bench Press',
            'type' => 'strength',
            'description' => null,
            'movement_pattern' => null,
            'mechanic' => null,
            'force_type' => null,
            'laterality' => null,
            'body_region' => null,
            'form_cues' => null,
        ]);

        (new EnrichExerciseJob($exercise, force: false))->handle();

        $exercise->refresh();

        expect($exercise->description)->not->toBeNull()
            ->and($exercise->movement_pattern)->not->toBeNull();

        ExerciseEnrichmentAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'Bench Press'));
    });

    it('skips soft-deleted exercises', function () {
        ExerciseEnrichmentAgent::fake();

        $exercise = Exercise::factory()->create(['deleted_at' => now()]);

        (new EnrichExerciseJob($exercise))->handle();

        ExerciseEnrichmentAgent::assertNeverPrompted();
    });

    it('skips when no fields need enrichment', function () {
        ExerciseEnrichmentAgent::fake();

        $exercise = Exercise::factory()->create([
            'movement_pattern' => 'squat',
            'mechanic' => 'compound',
            'force_type' => 'push',
            'laterality' => 'bilateral',
            'body_region' => 'lower_body',
            'primary_muscles' => ['quadriceps'],
            'secondary_muscles' => ['gluteus_maximus'],
            'equipment' => ['barbell'],
            'training_places' => ['gym'],
            'difficulty' => 'Intermediate',
            'description' => 'Existing description',
            'instructions' => ['Step 1'],
            'form_cues' => 'Existing cue',
        ]);

        (new EnrichExerciseJob($exercise, force: false))->handle();

        ExerciseEnrichmentAgent::assertNeverPrompted();
    });
});

describe('EnrichExerciseTranslationsJob', function () {
    it('creates translation via AI agent', function () {
        ExerciseTranslationAgent::fake();

        $exercise = Exercise::factory()->create([
            'name' => 'Bench Press',
            'description' => 'A compound pressing exercise.',
            'instructions' => ['Lie on bench', 'Press up'],
            'form_cues' => 'Retract scapula.',
        ]);

        (new EnrichExerciseTranslationsJob($exercise, 'de'))->handle();

        $translation = $exercise->translations()->where('locale', 'de')->first();

        expect($translation)->not->toBeNull()
            ->and($translation->name)->not->toBeNull();

        ExerciseTranslationAgent::assertPrompted(fn ($prompt) => str_contains($prompt->prompt, 'Bench Press'));
    });

    it('skips when exercise is not yet enriched', function () {
        ExerciseTranslationAgent::fake();

        $exercise = Exercise::factory()->create(['description' => null]);

        (new EnrichExerciseTranslationsJob($exercise, 'de'))->handle();

        ExerciseTranslationAgent::assertNeverPrompted();
        expect($exercise->translations()->count())->toBe(0);
    });

    it('upserts existing translation', function () {
        ExerciseTranslationAgent::fake();

        $exercise = Exercise::factory()->create([
            'description' => 'Existing',
            'instructions' => ['Step 1'],
            'form_cues' => 'Existing cue',
        ]);

        $exercise->translations()->create([
            'locale' => 'de',
            'name' => 'Bankdrücken',
        ]);

        (new EnrichExerciseTranslationsJob($exercise, 'de'))->handle();

        expect($exercise->translations()->where('locale', 'de')->count())->toBe(1);
    });
});
