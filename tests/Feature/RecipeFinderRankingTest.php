<?php

use App\Models\Recipe;
use App\Services\Recipe\FoodTermTranslator;
use App\Services\Recipe\RecipeFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Meilisearch\Client;
use Meilisearch\Endpoints\Indexes;

uses(RefreshDatabase::class);

/**
 * @param  list<int>  $hitIds
 */
function finderReturning(array $hitIds): RecipeFinder
{
    $result = Mockery::mock();
    $result->shouldReceive('getHits')->andReturn(array_map(fn (int $id) => ['id' => $id], $hitIds));

    $index = Mockery::mock(Indexes::class);
    $index->shouldReceive('search')->andReturn($result);

    $client = Mockery::mock(Client::class);
    $client->shouldReceive('index')->with('recipes')->andReturn($index);

    return new RecipeFinder($client, app(FoodTermTranslator::class));
}

it('ranks by protein closest to target when calories are equal', function () {
    $low = Recipe::factory()->create(['calories' => 700, 'protein_g' => 20]);
    $onTarget = Recipe::factory()->create(['calories' => 700, 'protein_g' => 35]);
    $high = Recipe::factory()->create(['calories' => 700, 'protein_g' => 60]);

    $pick = finderReturning([$low->id, $onTarget->id, $high->id])->findCandidate(
        mealType: 'lunch', targetKcal: 700, locale: 'de',
        allowedProteins: [], dislikes: [], forbiddenAxes: collect(),
        targetProtein: 34,
    );

    expect($pick->id)->toBe($onTarget->id);
});

it('ranks by calories closest to target when protein is equal', function () {
    $under = Recipe::factory()->create(['calories' => 600, 'protein_g' => 40]);
    $onTarget = Recipe::factory()->create(['calories' => 700, 'protein_g' => 40]);
    $over = Recipe::factory()->create(['calories' => 800, 'protein_g' => 40]);

    $pick = finderReturning([$under->id, $onTarget->id, $over->id])->findCandidate(
        mealType: 'lunch', targetKcal: 700, locale: 'de',
        allowedProteins: [], dislikes: [], forbiddenAxes: collect(),
        targetProtein: 40,
    );

    expect($pick->id)->toBe($onTarget->id);
});

it('falls back to the highest-protein recipe when no target is given', function () {
    $low = Recipe::factory()->create(['calories' => 700, 'protein_g' => 20]);
    $mid = Recipe::factory()->create(['calories' => 700, 'protein_g' => 35]);
    $high = Recipe::factory()->create(['calories' => 700, 'protein_g' => 60]);

    $pick = finderReturning([$low->id, $mid->id, $high->id])->findCandidate(
        mealType: 'lunch', targetKcal: 700, locale: 'de',
        allowedProteins: [], dislikes: [], forbiddenAxes: collect(),
    );

    expect($pick->id)->toBe($high->id);
});
