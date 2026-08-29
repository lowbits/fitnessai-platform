<?php

use App\Models\HealthDailyMetric;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTracking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['health.credit_factor' => 0.5, 'health.credit_cap_kcal' => 500]);
});

function syncPayload(array $overrides = []): array
{
    return array_merge([
        'date' => today()->toDateString(),
        'active_energy_kcal' => 640,
        'steps' => 8000,
        'workouts' => [],
    ], $overrides);
}

/* -------------------------------- sync ---------------------------------- */

it('rejects guests', function () {
    postJson('/api/v3/health/daily-sync', syncPayload())->assertUnauthorized();
});

it('validates the payload', function () {
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/v3/health/daily-sync', syncPayload(['active_energy_kcal' => -1]))
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('active_energy_kcal');
});

it('upserts the day metric and returns the credited calories', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true]);
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload())
        ->assertOk()
        ->assertJsonPath('active_energy_kcal', 640)
        ->assertJsonPath('steps', 8000)
        ->assertJsonPath('credited_kcal', 320)
        ->assertJsonPath('enabled', true);

    expect(HealthDailyMetric::where('user_id', $user->id)->count())->toBe(1);
});

it('subtracts a completed fytrr workout before crediting so training is not double-counted', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true, 'workout_writeback_enabled' => true]);
    $plan = WorkoutPlan::factory()->create(['estimated_calories_burned' => 300]);
    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $plan->id,
        'completed_at' => today(),
    ]);
    Sanctum::actingAs($user);

    // 640 measured − 300 training = 340 creditable, × 0.5 = 170.
    postJson('/api/v3/health/daily-sync', syncPayload())
        ->assertOk()
        ->assertJsonPath('credited_kcal', 170);
});

it('subtracts the computed MET estimate when the workout has no stored calories', function () {
    $user = User::factory()->withProfile(['weight_kg' => 80])->create([
        'activity_credit_enabled' => true,
        'workout_writeback_enabled' => true,
    ]);
    $plan = WorkoutPlan::factory()->create([
        'workout_type' => 'hypertrophy',
        'estimated_duration_minutes' => 45,
        'estimated_calories_burned' => null,
    ]);
    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $plan->id,
        'completed_at' => today(),
    ]);
    Sanctum::actingAs($user);

    // 6.0 MET × 80 kg × 45/60 = 360 estimated; 640 − 360 = 280 creditable, × 0.5 = 140.
    postJson('/api/v3/health/daily-sync', syncPayload())
        ->assertOk()
        ->assertJsonPath('credited_kcal', 140);
});

it('adds workout energy to the active total before crediting, since HealthKit keeps it separate', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true, 'workout_writeback_enabled' => true]);
    $plan = WorkoutPlan::factory()->create(['estimated_calories_burned' => 314]);
    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $plan->id,
        'completed_at' => today(),
    ]);
    Sanctum::actingAs($user);

    // active 5 + workouts (314 fytrr + 5 run) = 324 total; − 314 fytrr training = 10; × 0.5 = 5.
    postJson('/api/v3/health/daily-sync', syncPayload([
        'active_energy_kcal' => 5,
        'workouts' => [
            ['type' => 'strength', 'start' => now()->toIso8601String(), 'end' => now()->toIso8601String(), 'energy_kcal' => 314, 'source' => null],
            ['type' => 'running', 'start' => now()->toIso8601String(), 'end' => now()->toIso8601String(), 'energy_kcal' => 5, 'source' => null],
        ],
    ]))
        ->assertOk()
        ->assertJsonPath('credited_kcal', 5);
});

it('does not subtract training when write-back is off (the workout never reached Health)', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true, 'workout_writeback_enabled' => false]);
    $plan = WorkoutPlan::factory()->create(['estimated_calories_burned' => 300]);
    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $plan->id,
        'completed_at' => today(),
    ]);
    Sanctum::actingAs($user);

    // Write-back off → the workout is not in active energy → nothing subtracted → 640 × 0.5 = 320.
    postJson('/api/v3/health/daily-sync', syncPayload())
        ->assertOk()
        ->assertJsonPath('credited_kcal', 320);
});

it('does not subtract a skipped (uncompleted) workout', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true]);
    $plan = WorkoutPlan::factory()->create(['estimated_calories_burned' => 300]);
    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'workout_plan_id' => $plan->id,
        'completed_at' => null,
    ]);
    Sanctum::actingAs($user);

    // No completed workout → nothing subtracted → 640 × 0.5 = 320.
    postJson('/api/v3/health/daily-sync', syncPayload())
        ->assertOk()
        ->assertJsonPath('credited_kcal', 320);
});

it('is idempotent on (user, date)', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true]);
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload(['active_energy_kcal' => 200]))->assertOk();
    postJson('/api/v3/health/daily-sync', syncPayload(['active_energy_kcal' => 900, 'steps' => 12000]))
        ->assertOk()
        ->assertJsonPath('credited_kcal', 450);

    expect(HealthDailyMetric::where('user_id', $user->id)->count())->toBe(1);
    $metric = HealthDailyMetric::where('user_id', $user->id)->first();
    expect($metric->active_energy_kcal)->toBe(900)
        ->and($metric->steps)->toBe(12000);
});

it('marks the user connected once and never moves the timestamp', function () {
    $user = User::factory()->create();
    expect($user->health_connected_at)->toBeNull();
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload())->assertOk();
    $firstConnectedAt = $user->fresh()->health_connected_at;
    expect($firstConnectedAt)->not->toBeNull();

    $this->travel(1)->hours();
    postJson('/api/v3/health/daily-sync', syncPayload(['date' => today()->addDay()->toDateString()]))->assertOk();

    expect($user->fresh()->health_connected_at->toIso8601String())
        ->toBe($firstConnectedAt->toIso8601String());
});

it('stores the full credit but gates the response when the toggle is off', function () {
    $user = User::factory()->create(['activity_credit_enabled' => false]);
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload())
        ->assertOk()
        ->assertJsonPath('credited_kcal', 0)
        ->assertJsonPath('enabled', false);

    // The full credit is still stored, so re-enabling applies it without a re-sync.
    expect(HealthDailyMetric::where('user_id', $user->id)->value('credited_kcal'))->toBe(320);
});

/* ------------------------------ settings -------------------------------- */

it('toggles the activity credit flag', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true]);
    Sanctum::actingAs($user);

    patchJson('/api/v3/health/settings', ['activity_credit_enabled' => false])
        ->assertOk()
        ->assertJsonPath('activity_credit_enabled', false);

    expect($user->fresh()->activity_credit_enabled)->toBeFalse();
});

it('toggles the workout write-back flag', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    patchJson('/api/v3/health/settings', ['workout_writeback_enabled' => false])
        ->assertOk()
        ->assertJsonPath('workout_writeback_enabled', false);

    expect($user->fresh()->workout_writeback_enabled)->toBeFalse();
});

it('rejects an empty settings payload', function () {
    Sanctum::actingAs(User::factory()->create());

    patchJson('/api/v3/health/settings', [])->assertStatus(422);
});

it('disconnects apple health, clearing metrics and the connection', function () {
    $user = User::factory()->create(['activity_credit_enabled' => false]);
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload())->assertOk();
    expect($user->fresh()->health_connected_at)->not->toBeNull()
        ->and(HealthDailyMetric::where('user_id', $user->id)->count())->toBe(1);

    deleteJson('/api/v3/health/connection')->assertNoContent();

    $fresh = $user->fresh();
    expect($fresh->health_connected_at)->toBeNull()
        ->and($fresh->activity_credit_enabled)->toBeFalse()
        ->and(HealthDailyMetric::where('user_id', $user->id)->count())->toBe(0);
});

/* --------------------------- day payload block -------------------------- */

it('exposes the activity block with the credited budget on the day payload', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true]);
    $start = now()->startOfDay();
    Plan::factory()->active()->create([
        'user_id' => $user->id,
        'start_date' => $start,
        'duration_days' => 3,
        'end_date' => $start->copy()->addDays(2),
        'generation_completed_at' => now(),
    ]);
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload())->assertOk();

    getJson('/api/v3/plan/day/'.today()->toDateString())
        ->assertOk()
        ->assertJsonPath('activity.connected', true)
        ->assertJsonPath('activity.enabled', true)
        ->assertJsonPath('activity.measured', 640)
        ->assertJsonPath('activity.steps', 8000)
        ->assertJsonPath('activity.credited', 320)
        ->assertJsonPath('activity.writeback_enabled', true);
});

it('folds workout energy into the day payload measured, since HealthKit keeps it separate', function () {
    $user = User::factory()->create(['activity_credit_enabled' => true]);
    $start = now()->startOfDay();
    Plan::factory()->active()->create([
        'user_id' => $user->id,
        'start_date' => $start,
        'duration_days' => 3,
        'end_date' => $start->copy()->addDays(2),
        'generation_completed_at' => now(),
    ]);
    Sanctum::actingAs($user);

    postJson('/api/v3/health/daily-sync', syncPayload([
        'active_energy_kcal' => 5,
        'workouts' => [
            ['type' => 'strength', 'start' => now()->toIso8601String(), 'end' => now()->toIso8601String(), 'energy_kcal' => 314, 'source' => null],
        ],
    ]))->assertOk();

    getJson('/api/v3/plan/day/'.today()->toDateString())
        ->assertOk()
        ->assertJsonPath('activity.measured', 319) // 5 active + 314 workout
        ->assertJsonPath('activity.active_energy', 5)
        ->assertJsonPath('activity.workout_energy', 314)
        ->assertJsonPath('activity.training_subtracted', 0);
});

it('reports a disconnected empty activity block before any sync', function () {
    $user = User::factory()->create();
    $start = now()->startOfDay();
    Plan::factory()->active()->create([
        'user_id' => $user->id,
        'start_date' => $start,
        'duration_days' => 3,
        'end_date' => $start->copy()->addDays(2),
        'generation_completed_at' => now(),
    ]);
    Sanctum::actingAs($user);

    getJson('/api/v3/plan/day/'.today()->toDateString())
        ->assertOk()
        ->assertJsonPath('activity.connected', false)
        ->assertJsonPath('activity.measured', null)
        ->assertJsonPath('activity.credited', 0);
});
