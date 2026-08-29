<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

beforeEach(fn () => config(['subscription.preview_days' => 1]));

/**
 * Active 3-day plan starting today, generation already finished — the baseline
 * every test tweaks. Returns [plan, start] so tests can address days by offset.
 */
function planStartingToday(User $user, int $durationDays = 3): Plan
{
    $start = now()->startOfDay();

    return Plan::factory()->active()->create([
        'user_id' => $user->id,
        'start_date' => $start,
        'duration_days' => $durationDays,
        'end_date' => $start->copy()->addDays($durationDays - 1),
        'generation_completed_at' => now(),
    ]);
}

function dayUrl(Plan $plan, int $offset): string
{
    $date = $plan->start_date->copy()->addDays($offset)->format('Y-m-d');

    return "/api/v3/plan/day/{$date}";
}

function generateDay(Plan $plan, int $offset, string $status = 'generated'): void
{
    $date = $plan->start_date->copy()->addDays($offset)->toDateString();

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => $date,
        'status' => $status,
    ]);
    Meal::factory()->count(3)->create(['meal_plan_id' => $mealPlan->id, 'status' => $status]);

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => $date,
        'status' => $status,
    ]);
}

it('computes estimated_calories_burned from MET when the workout has none stored', function () {
    $user = User::factory()->withProfile(['weight_kg' => 80])->create();
    Sanctum::actingAs($user);
    $plan = planStartingToday($user);

    $date = $plan->start_date->copy()->toDateString();
    MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'generated']);
    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => $date,
        'day_number' => 1,
        'status' => 'generated',
        'workout_type' => 'hypertrophy',
        'estimated_duration_minutes' => 45,
        'estimated_calories_burned' => null,
    ]);

    // 6.0 MET × 80 kg × (45/60) h = 360
    getJson(dayUrl($plan, 0))
        ->assertOk()
        ->assertJsonPath('workout.estimated_calories_burned', 360);
});

it('rejects guests', function () {
    getJson('/api/v3/plan/day/2026-01-01')->assertUnauthorized();
});

it('404s when the user has no active plan', function () {
    Sanctum::actingAs(User::factory()->create());

    getJson('/api/v3/plan/day/2026-01-01')->assertNotFound();
});

it('reports the meal plan as generated even after all meals are removed', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $plan = planStartingToday($user);
    generateDay($plan, 0);

    Meal::query()->delete();

    getJson(dayUrl($plan, 0))
        ->assertOk()
        ->assertJsonPath('meal_plan_status', 'generated')
        ->assertJsonCount(0, 'meals');
});

it('422s on a malformed date', function () {
    $user = User::factory()->create();
    planStartingToday($user);
    Sanctum::actingAs($user);

    getJson('/api/v3/plan/day/not-a-date')->assertStatus(422);
});

it('does not expose the removed plan_day field', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    generateDay($plan, 0);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))->assertOk()->assertJsonMissingPath('plan_day');
});

/* -------------------------------- access -------------------------------- */

it('grants full access on day 1', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    generateDay($plan, 0);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))
        ->assertOk()
        ->assertJsonPath('access', 'full')
        ->assertJsonPath('status', 'generated')
        ->assertJsonCount(3, 'meals')
        ->assertJsonPath('workout.status', 'generated');
});

it('locks day 1 too when preview_days is 0', function () {
    config(['subscription.preview_days' => 0]);
    $user = User::factory()->trialExpired()->create();
    $plan = planStartingToday($user);
    generateDay($plan, 0);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))->assertOk()->assertJsonPath('access', 'preview');
});

it('previews day 2 for a user without an active subscription', function () {
    $user = User::factory()->trialExpired()->create();
    $plan = planStartingToday($user);
    generateDay($plan, 1);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 1))
        ->assertOk()
        ->assertJsonPath('access', 'preview')
        ->assertJsonCount(3, 'meals'); // teaser content still shipped
});

it('grants full access to later days when the subscription is active', function () {
    $user = User::factory()->onTrial()->create();
    $plan = planStartingToday($user);
    generateDay($plan, 1);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 1))->assertOk()->assertJsonPath('access', 'full');
});

it('marks days beyond the plan as expired with no content', function () {
    $user = User::factory()->trialExpired()->create();
    $plan = planStartingToday($user);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 3)) // duration is 3 → day 4 is beyond
        ->assertOk()
        ->assertJsonPath('access', 'expired')
        ->assertJsonPath('meals', [])
        ->assertJsonPath('workout', null)
        ->assertJsonPath('plan_end_date', $plan->end_date->format('Y-m-d'));
});

it('replays a rotating generated day as the expired teaser, not yesterday', function () {
    $user = User::factory()->trialExpired()->create();
    $plan = planStartingToday($user);

    foreach (range(0, 2) as $offset) {
        $date = $plan->start_date->copy()->addDays($offset)->toDateString();
        $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'day_number' => $offset + 1, 'status' => 'generated']);
        Meal::factory()->create(['meal_plan_id' => $mealPlan->id, 'type' => 'breakfast', 'name' => "Day{$offset}", 'status' => 'generated']);
        WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'day_number' => $offset + 1, 'status' => 'generated']);
    }
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 3))
        ->assertOk()
        ->assertJsonPath('access', 'expired')
        ->assertJsonPath('meals.0.name', 'Day0')
        ->assertJsonPath('workout.status', 'generated');
});

it('marks dates before the plan start as before_start', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, -1))
        ->assertOk()
        ->assertJsonPath('access', 'before_start')
        ->assertJsonPath('meals', []);
});

/* ------------------------------ generation ------------------------------ */

it('reports not_generated when no content exists yet', function () {
    $user = User::factory()->onTrial()->create();
    $plan = planStartingToday($user);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 1)) // accessible (active sub) but nothing generated
        ->assertOk()
        ->assertJsonPath('access', 'full')
        ->assertJsonPath('status', 'not_generated')
        ->assertJsonPath('workout', null);
});

it('reports generating while a meal plan is pending', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'date' => $plan->start_date->toDateString(),
        'status' => 'pending',
    ]);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))->assertOk()->assertJsonPath('status', 'generating');
});

it('reports generating on day 1 before generation completes', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    $plan->update(['generation_completed_at' => null]);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))->assertOk()->assertJsonPath('status', 'generating');
});

it('reports partial when one side fails and the other generates', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    $date = $plan->start_date->toDateString();
    MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'generated']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'failed']);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))->assertOk()->assertJsonPath('status', 'partial');
});

it('reports failed when both sides fail', function () {
    $user = User::factory()->create();
    $plan = planStartingToday($user);
    $date = $plan->start_date->toDateString();
    MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'failed']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'failed']);
    Sanctum::actingAs($user);

    getJson(dayUrl($plan, 0))->assertOk()->assertJsonPath('status', 'failed');
});
