<?php

use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('toSearchableArray returns the expected fields', function () {
    $exercise = Exercise::factory()->create([
        'name' => 'Bench Press',
        'type' => 'strength',
        'movement_pattern' => 'horizontal push',
        'mechanic' => 'compound',
        'body_region' => 'upper',
        'difficulty' => 'intermediate',
        'description' => 'A chest exercise',
        'primary_muscles' => ['chest', 'triceps'],
        'secondary_muscles' => ['shoulders'],
        'equipment' => ['barbell', 'flat bench'],
        'training_places' => ['gym'],
        'event_tags' => ['push day'],
        'is_verified' => true,
    ]);

    $result = $exercise->toSearchableArray();

    expect($result)->toHaveKeys([
        'id',
        'name',
        'slug',
        'translations',
        'all_names',
        'search_text',
        'type',
        'movement_pattern',
        'mechanic',
        'body_region',
        'difficulty',
        'primary_muscles',
        'secondary_muscles',
        'equipment',
        'training_places',
        'event_tags',
        'is_verified',
    ])->and($result['id'])->toBe($exercise->id)
        ->and($result['name'])->toBe('Bench Press')
        ->and($result['type'])->toBe('strength')
        ->and($result['is_verified'])->toBeTrue()
        ->and($result['primary_muscles'])->toBe(['chest', 'triceps'])
        ->and($result['equipment'])->toBe(['barbell', 'flat bench']);

});

test('toSearchableArray includes translations keyed by locale', function () {
    $exercise = Exercise::factory()
        ->withTranslations(['de' => 'Bankdrücken'])
        ->create(['name' => 'Bench Press']);

    $result = $exercise->toSearchableArray();

    expect($result['translations'])->toBe([
        'de' => 'Bankdrücken',
        'en' => 'Bench Press',
    ]);
});

test('toSearchableArray includes translated names and aliases in all_names', function () {
    $exercise = Exercise::factory()->create(['name' => 'Bench Press']);

    ExerciseTranslation::create([
        'exercise_id' => $exercise->id,
        'locale' => 'de',
        'name' => 'Bankdrücken',
        'aliases' => ['Flachbankdrücken'],
    ]);

    $exercise->load('translations');
    $result = $exercise->toSearchableArray();

    expect($result['all_names'])->toContain('Bench Press')
        ->and($result['all_names'])->toContain('Bankdrücken')
        ->and($result['all_names'])->toContain('Flachbankdrücken');
});

test('toSearchableArray builds search_text from key fields', function () {
    $exercise = Exercise::factory()->create([
        'name' => 'Squat',
        'description' => 'A leg exercise',
        'type' => 'strength',
        'movement_pattern' => 'squat',
        'mechanic' => 'compound',
        'body_region' => 'lower',
    ]);

    $result = $exercise->toSearchableArray();

    expect($result['search_text'])->toBe('Squat | A leg exercise | strength | squat | compound | lower');
});

test('toSearchableArray defaults empty arrays for nullable array fields', function () {
    $exercise = Exercise::factory()->create([
        'primary_muscles' => null,
        'secondary_muscles' => null,
        'equipment' => null,
        'training_places' => null,
        'event_tags' => null,
    ]);

    $result = $exercise->toSearchableArray();

    expect($result['primary_muscles'])->toBe([])
        ->and($result['secondary_muscles'])->toBe([])
        ->and($result['equipment'])->toBe([])
        ->and($result['training_places'])->toBe([])
        ->and($result['event_tags'])->toBe([]);
});
