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

function weekPlan(User $user): Plan
{
    $start = now()->startOfDay();

    return Plan::factory()->active()->create([
        'user_id' => $user->id,
        'start_date' => $start,
        'duration_days' => 7,
        'end_date' => $start->copy()->addDays(6),
        'generation_completed_at' => now(),
    ]);
}

function weekGenerate(Plan $plan, int $offset): void
{
    $date = $plan->start_date->copy()->addDays($offset)->toDateString();
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'generated']);
    Meal::factory()->count(3)->create(['meal_plan_id' => $mealPlan->id, 'status' => 'generated']);
    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'date' => $date, 'status' => 'generated']);
}

it('rejects guests', function () {
    getJson('/api/v3/plan/week')->assertUnauthorized();
});

it('404s without an active plan', function () {
    Sanctum::actingAs(User::factory()->create());

    getJson('/api/v3/plan/week')->assertNotFound();
});

it('is not ready until the first day is generated', function () {
    $user = User::factory()->create();
    weekPlan($user);
    Sanctum::actingAs($user);

    getJson('/api/v3/plan/week')
        ->assertOk()
        ->assertJsonCount(7, 'days')
        ->assertJsonPath('ready', false)
        ->assertJsonPath('days_generated', 0)
        ->assertJsonPath('days.0.status', 'not_generated')
        ->assertJsonPath('days.0.workout', null);
});

it('summarises the week once days are generated', function () {
    $user = User::factory()->create();
    $plan = weekPlan($user);
    weekGenerate($plan, 0);
    weekGenerate($plan, 1);
    Sanctum::actingAs($user);

    getJson('/api/v3/plan/week')
        ->assertOk()
        ->assertJsonCount(7, 'days')
        ->assertJsonPath('ready', true)
        ->assertJsonPath('days_generated', 2)
        ->assertJsonPath('days.0.status', 'generated')
        ->assertJsonCount(3, 'days.0.meals')
        ->assertJsonPath('days.1.workout.duration_minutes', fn ($v) => $v !== null)
        ->assertJsonPath('days.2.status', 'not_generated')
        ->assertJsonPath('days.2.meals', []);
});
