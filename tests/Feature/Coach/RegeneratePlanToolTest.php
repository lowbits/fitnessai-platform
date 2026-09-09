<?php

use App\Actions\RegenerateRemainingPlan;
use App\Ai\Tools\RegeneratePlanTool;
use App\Models\Plan;
use App\Models\User;
use Laravel\Ai\Tools\Request;

function regeneratePlan(User $user, array $args, ?RegenerateRemainingPlan $action = null): array
{
    $action ??= Mockery::mock(RegenerateRemainingPlan::class);

    return json_decode((new RegeneratePlanTool($user, $action))->handle(new Request($args)), true);
}

it('errors when the user has no active plan', function () {
    expect(regeneratePlan(User::factory()->create(), ['confirmed' => true])['error'])->toBe('no_active_plan');
});

it('asks for confirmation before rebuilding', function () {
    $user = User::factory()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);

    expect(regeneratePlan($user, ['confirmed' => false])['requires_confirmation'])->toBeTrue();
});

it('rebuilds once confirmed, then throttles an immediate repeat', function () {
    $user = User::factory()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);

    $action = Mockery::mock(RegenerateRemainingPlan::class);
    $action->shouldReceive('execute')->once()->andReturn(['from_day' => 4, 'meal_days' => 2, 'workout_days' => 2]);

    expect(regeneratePlan($user, ['confirmed' => true], $action)['regenerating'])->toBeTrue()
        ->and(regeneratePlan($user, ['confirmed' => true], $action)['error'])->toBe('throttled');
});

it('releases the throttle and reports failure when the rebuild throws', function () {
    $user = User::factory()->create();
    Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);

    $failing = Mockery::mock(RegenerateRemainingPlan::class);
    $failing->shouldReceive('execute')->once()->andThrow(new RuntimeException('boom'));

    expect(regeneratePlan($user, ['confirmed' => true], $failing)['error'])->toBe('regen_failed');

    // lock was released, so a follow-up rebuild is not throttled
    $ok = Mockery::mock(RegenerateRemainingPlan::class);
    $ok->shouldReceive('execute')->once()->andReturn(['from_day' => 4, 'meal_days' => 1, 'workout_days' => 1]);

    expect(regeneratePlan($user, ['confirmed' => true], $ok)['regenerating'])->toBeTrue();
});
