<?php

use App\Models\Recipe;
use App\Services\Recipe\DislikeFilter;
use App\Services\Recipe\RecipeFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Meilisearch\Client;
use Meilisearch\Endpoints\Indexes;

uses(RefreshDatabase::class);

/**
 * @param  list<int>  $hitIds
 */
function finderCapturingFilter(array $hitIds, ?string &$captured): RecipeFinder
{
    $result = Mockery::mock();
    $result->shouldReceive('getHits')->andReturn(array_map(fn (int $id) => ['id' => $id], $hitIds));

    $index = Mockery::mock(Indexes::class);
    $index->shouldReceive('search')->andReturnUsing(function ($query, $params) use ($result, &$captured) {
        $captured = $params['filter'];

        return $result;
    });

    $client = Mockery::mock(Client::class);
    $client->shouldReceive('index')->with('recipes')->andReturn($index);

    return new RecipeFinder($client, app(DislikeFilter::class));
}

it('excludes soy/nut foods and keeps the slot on a wish-driven swap', function () {
    $recipe = Recipe::factory()->create();
    $captured = null;

    finderCapturingFilter([$recipe->id], $captured)->findCandidates(
        mealType: 'dinner',
        targetKcal: 650,
        locale: 'de',
        allowedProteins: ['chicken', 'beef'],
        dislikes: ['soy', 'nuts'],
        forbiddenAxes: collect(),
        query: 'etwas Größeres ohne Tofu',
        constrainSlot: true,
        constrainCalories: false,
    );

    expect($captured)
        ->toContain('meal_types = "dinner"')
        ->toContain('primary_protein != "tofu"')
        ->toContain('allergens != "soy"')
        ->toContain('allergens != "nuts"')
        ->not->toContain('calories ');
});

it('keeps the calorie band for plain plan generation', function () {
    $recipe = Recipe::factory()->create();
    $captured = null;

    finderCapturingFilter([$recipe->id], $captured)->findCandidate(
        mealType: 'lunch',
        targetKcal: 700,
        locale: 'de',
        allowedProteins: [],
        dislikes: [],
        forbiddenAxes: collect(),
    );

    expect($captured)
        ->toContain('meal_types = "lunch"')
        ->toContain('calories ');
});
