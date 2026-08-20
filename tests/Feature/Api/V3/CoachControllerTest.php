<?php

use App\Ai\Agents\MonaCoachAgent;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function coachMealForUser(): array
{
    $user = User::factory()->withProfile()->onTrial()->create();
    $plan = Plan::factory()->create(['user_id' => $user->id, 'status' => 'active']);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'status' => 'generated']);
    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'type' => 'lunch',
        'name' => 'Old Lunch',
        'calories' => 500,
        'protein_g' => 40,
        'carbs_g' => 35,
        'fat_g' => 20,
    ]);

    return [$user, $meal];
}

test('returns the message-part contract with a text part', function () {
    MonaCoachAgent::fake(['Klar, wie kann ich dir helfen?']);

    $user = User::factory()->withProfile()->onTrial()->create();

    $response = $this->actingAs($user)->postJson(route('v3.coach.messages'), [
        'message' => 'Hallo Mona',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'conversation_id',
        'message' => ['role', 'parts'],
    ]);
    $response->assertJsonPath('message.role', 'assistant');
    $response->assertJsonPath('message.parts.0.type', 'text');
    $response->assertJsonPath('message.parts.0.text', 'Klar, wie kann ich dir helfen?');

    MonaCoachAgent::assertPrompted(fn () => true);
});

test('persists a conversation for the user', function () {
    MonaCoachAgent::fake(['Hi!']);

    $user = User::factory()->withProfile()->onTrial()->create();

    $this->actingAs($user)->postJson(route('v3.coach.messages'), [
        'message' => 'Hallo',
    ])->assertStatus(200);

    expect(DB::table('agent_conversations')
        ->where('participant_type', $user::class)
        ->where('participant_id', $user->id)
        ->count())->toBe(1);
});

test('requires authentication', function () {
    $this->postJson(route('v3.coach.messages'), ['message' => 'Hallo'])
        ->assertUnauthorized();
});

test('answers a free user with a paywall upsell instead of serving Mona', function () {
    $user = User::factory()->withProfile()->create();

    $this->actingAs($user)->postJson(route('v3.coach.messages'), [
        'message' => 'Hallo',
    ])
        ->assertStatus(402)
        ->assertJsonPath('error', 'subscription_required');
});

test('validates the message is present', function () {
    MonaCoachAgent::fake(['Hi!']);

    $user = User::factory()->withProfile()->onTrial()->create();

    $this->actingAs($user)->postJson(route('v3.coach.messages'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('message');
});

test('rejects continuing another user\'s conversation', function () {
    MonaCoachAgent::fake(['Hi!']);

    $owner = User::factory()->withProfile()->onTrial()->create();
    $intruder = User::factory()->withProfile()->onTrial()->create();

    $conversationId = (string) Str::uuid();
    DB::table('agent_conversations')->insert([
        'id' => $conversationId,
        'participant_type' => $owner::class,
        'participant_id' => $owner->id,
        'title' => 'Owner conversation',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($intruder)->postJson(route('v3.coach.messages'), [
        'conversation_id' => $conversationId,
        'message' => 'let me in',
    ])->assertForbidden();
});

test('accepts a meal_replace context for an owned meal', function () {
    MonaCoachAgent::fake(['Klar, tauschen wir!']);

    [$user, $meal] = coachMealForUser();

    $this->actingAs($user)->postJson(route('v3.coach.messages'), [
        'message' => 'ersetzen',
        'context' => ['type' => 'meal_replace', 'meal_id' => $meal->id],
    ])
        ->assertStatus(200)
        ->assertJsonPath('message.parts.0.type', 'text');
});

test('forbids a meal_replace context for a meal the user does not own', function () {
    MonaCoachAgent::fake(['Hi!']);

    [, $meal] = coachMealForUser();
    $intruder = User::factory()->withProfile()->onTrial()->create();

    $this->actingAs($intruder)->postJson(route('v3.coach.messages'), [
        'message' => 'ersetzen',
        'context' => ['type' => 'meal_replace', 'meal_id' => $meal->id],
    ])->assertForbidden();
});
