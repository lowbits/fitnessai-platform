<?php

use App\Support\PlanEvaluator\Dimension;
use App\Support\PlanEvaluator\Rating;
use App\Support\PlanEvaluator\WorkoutRubric;

function rubric(): WorkoutRubric
{
    return new WorkoutRubric;
}

/**
 * A balanced, progressive, twice-a-week plan across every major group.
 *
 * @return array<string, mixed>
 */
function goodPlanFacts(): array
{
    return [
        'plan_type' => 'workout',
        'days_per_week' => 5,
        'has_progression' => true,
        'intensity' => 'hard',
        'muscle_groups' => collect(['legs', 'back', 'chest', 'shoulders', 'arms', 'core'])
            ->map(fn (string $group) => ['group' => $group, 'weekly_sets' => 14, 'sessions_per_week' => 2])
            ->all(),
    ];
}

/**
 * @param  array<string, mixed>  $facts
 */
function ratingFor(string $key, array $facts): Rating
{
    return collect(rubric()->score($facts)['dimensions'])->firstWhere('key', $key)->rating;
}

it('scores a balanced progressive plan highly', function () {
    $result = rubric()->score(goodPlanFacts());

    expect($result['score'])->toBe(100)
        ->and(collect($result['dimensions'])->every(fn (Dimension $d) => $d->rating === Rating::Good))->toBeTrue();
});

it('flags a plan with no leg or back training', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect($facts['muscle_groups'])
        ->reject(fn (array $group) => in_array($group['group'], ['legs', 'back'], true))
        ->values()
        ->all();

    expect(ratingFor('coverage', $facts))->toBe(Rating::Poor)
        ->and(rubric()->score($facts)['score'])->toBeLessThan(80);
});

it('treats a muscle group with zero weekly sets as not trained', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect($facts['muscle_groups'])
        ->map(fn (array $group) => in_array($group['group'], ['legs', 'back'], true)
            ? [...$group, 'weekly_sets' => 0]
            : $group)
        ->all();

    expect(ratingFor('coverage', $facts))->toBe(Rating::Poor);
});

it('marks a single missing group as ok, not poor', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect($facts['muscle_groups'])
        ->reject(fn (array $group) => $group['group'] === 'core')
        ->values()
        ->all();

    expect(ratingFor('coverage', $facts))->toBe(Rating::Ok);
});

it('flags a plan that piles volume on one group and starves another', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect($facts['muscle_groups'])
        ->map(function (array $group) {
            if ($group['group'] === 'chest') {
                return [...$group, 'weekly_sets' => 24];
            }
            if ($group['group'] === 'legs') {
                return [...$group, 'weekly_sets' => 6];
            }

            return $group;
        })
        ->all();

    expect(ratingFor('balance', $facts))->toBe(Rating::Poor);
});

it('rates even volume across groups as balanced', function () {
    expect(ratingFor('balance', goodPlanFacts()))->toBe(Rating::Good);
});

it('does not flag a large region carrying more sets as imbalanced', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect([
        ['group' => 'chest', 'weekly_sets' => 6],
        ['group' => 'back', 'weekly_sets' => 12],
        ['group' => 'shoulders', 'weekly_sets' => 6],
        ['group' => 'arms', 'weekly_sets' => 12],
        ['group' => 'legs', 'weekly_sets' => 24],
        ['group' => 'core', 'weekly_sets' => 6],
    ])->map(fn (array $group) => [...$group, 'sessions_per_week' => 2])->all();

    expect(ratingFor('balance', $facts))->toBe(Rating::Ok);
});

it('rates hard intensity good, light poor, and unspecified neutral', function () {
    expect(ratingFor('intensity', [...goodPlanFacts(), 'intensity' => 'hard']))->toBe(Rating::Good)
        ->and(ratingFor('intensity', [...goodPlanFacts(), 'intensity' => 'light']))->toBe(Rating::Poor)
        ->and(ratingFor('intensity', [...goodPlanFacts(), 'intensity' => 'unspecified']))->toBe(Rating::Ok);
});

it('flags missing progressive overload', function () {
    $facts = goodPlanFacts();
    $facts['has_progression'] = false;

    expect(ratingFor('progression', $facts))->toBe(Rating::Poor);
});

it('rates volume below the productive range as ok, not good', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect($facts['muscle_groups'])
        ->map(fn (array $group) => [...$group, 'weekly_sets' => 9])
        ->all();

    expect(ratingFor('volume', $facts))->toBe(Rating::Ok);
});

it('flags excessive volume on a group', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'][4] = ['group' => 'arms', 'weekly_sets' => 30, 'sessions_per_week' => 2];

    expect(ratingFor('volume', $facts))->not->toBe(Rating::Good);
});

it('flags muscles trained only once a week', function () {
    $facts = goodPlanFacts();
    $facts['muscle_groups'] = collect($facts['muscle_groups'])
        ->map(fn (array $group) => [...$group, 'sessions_per_week' => 1])
        ->all();

    expect(ratingFor('frequency', $facts))->toBe(Rating::Poor);
});

it('flags training seven days a week as poor recovery', function () {
    $facts = goodPlanFacts();
    $facts['days_per_week'] = 7;

    expect(ratingFor('recovery', $facts))->toBe(Rating::Poor);
});

it('scores a plan with no trackable sets at the floor', function () {
    $facts = ['plan_type' => 'workout', 'days_per_week' => 0, 'has_progression' => false, 'muscle_groups' => []];

    expect(rubric()->score($facts)['score'])->toBeLessThan(20)
        ->and(ratingFor('volume', $facts))->toBe(Rating::Poor);
});
