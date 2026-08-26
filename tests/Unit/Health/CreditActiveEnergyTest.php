<?php

use App\Actions\Health\CreditActiveEnergy;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config(['health.credit_factor' => 0.5, 'health.credit_cap_kcal' => 500]);
});

function activityCredit(int $kcal): int
{
    return app(CreditActiveEnergy::class)($kcal);
}

it('credits nothing for zero or negative energy', function () {
    expect(activityCredit(0))->toBe(0)
        ->and(activityCredit(-100))->toBe(0);
});

it('credits half the active energy', function () {
    expect(activityCredit(100))->toBe(50)
        ->and(activityCredit(640))->toBe(320);
});

it('rounds the credit down to the nearest 5', function () {
    // 333 * 0.5 = 166.5 -> floor 166 -> floor5 165
    expect(activityCredit(333))->toBe(165);
});

it('caps the daily credit', function () {
    // 2000 * 0.5 = 1000 -> capped at 500
    expect(activityCredit(2000))->toBe(500)
        ->and(activityCredit(1000))->toBe(500);
});

it('honours a tuned factor and cap from config', function () {
    config(['health.credit_factor' => 1.0, 'health.credit_cap_kcal' => 1000]);

    expect(activityCredit(700))->toBe(700)
        ->and(activityCredit(1500))->toBe(1000);
});
