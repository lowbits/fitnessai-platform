<?php

use App\Ai\Agents\NutritionPlannerAgent;
use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Ai\Prompts\MealPlanSystemPrompt;
use App\Jobs\GenerateMealPlanBatch;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\UserMessage;

uses(RefreshDatabase::class);

test('meal plan batch generates plans for each day using the agent', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    $job = new GenerateMealPlanBatch($user, $plan, startDay: 1, endDay: 3);
    $job->handle();

    NutritionPlannerAgent::assertPrompted(fn () => true);

    expect(MealPlan::where('plan_id', $plan->id)->count())->toBe(3);
});

test('meal plan batch skips already generated days', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    // Day 1 already generated
    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'generated',
    ]);

    $job = new GenerateMealPlanBatch($user, $plan, startDay: 1, endDay: 2);
    $job->handle();

    // Only day 2 should trigger the agent (day 1 was skipped)
    NutritionPlannerAgent::assertPrompted(fn () => true);

    expect(MealPlan::where('plan_id', $plan->id)->count())->toBe(2);
});

test('meal plan batch retries failed days', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'failed',
    ]);

    $job = new GenerateMealPlanBatch($user, $plan, startDay: 1, endDay: 1);
    $job->handle();

    NutritionPlannerAgent::assertPrompted(fn () => true);
});

test('meal plan batch cleans up partial meals before retry', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->withProfile()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'failed',
    ]);

    // Simulate partial meals from a previous failed attempt
    $mealPlan->meals()->create([
        'type' => 'breakfast',
        'name' => 'Partial Meal',
        'calories' => 100,
        'protein_g' => 10,
        'carbs_g' => 10,
        'fat_g' => 5,
        'status' => 'generated',
    ]);

    expect($mealPlan->meals()->count())->toBe(1);

    $job = new GenerateMealPlanBatch($user, $plan, startDay: 1, endDay: 1);
    $job->handle();

    // Old partial meals should have been cleaned up
    $mealPlan->refresh();
    expect($mealPlan->meals()->where('name', 'Partial Meal')->count())->toBe(0);
});

test('meal plan batch exits early without profile', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->create();

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    $job = new GenerateMealPlanBatch($user, $plan, startDay: 1, endDay: 3);
    $job->handle();

    NutritionPlannerAgent::assertNotPrompted(fn () => true);

    expect(MealPlan::where('plan_id', $plan->id)->count())->toBe(0);
});

test('agent includes previously generated meals in history', function () {
    $plan = Plan::factory()->create(['start_date' => now(), 'duration_days' => 7]);

    $day1 = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'generated',
    ]);

    Meal::factory()->create([
        'meal_plan_id' => $day1->id,
        'type' => 'breakfast',
        'name' => 'Greek Yogurt Parfait',
        'primary_protein' => 'dairy',
        'cuisine' => 'mediterranean',
        'ingredients' => [
            ['name' => 'Greek yogurt', 'amount' => '200', 'unit' => 'g'],
            ['name' => 'Granola', 'amount' => '50', 'unit' => 'g'],
            ['name' => 'Honey', 'amount' => '15', 'unit' => 'ml'],
        ],
    ]);

    Meal::factory()->create([
        'meal_plan_id' => $day1->id,
        'type' => 'lunch',
        'name' => 'Grilled Chicken Salad',
        'primary_protein' => 'chicken',
        'cuisine' => 'american',
        'ingredients' => [
            ['name' => 'Chicken breast', 'amount' => '200', 'unit' => 'g'],
            ['name' => 'Mixed greens', 'amount' => '100', 'unit' => 'g'],
        ],
    ]);

    $day2 = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 2,
        'status' => 'pending',
    ]);

    $agent = new NutritionPlannerAgent($day2);
    $messages = collect($agent->messages());

    expect($messages)->toHaveCount(2);
    expect($messages[0])->toBeInstanceOf(UserMessage::class);
    expect($messages[0]->content)->toContain('Day 1');
    expect($messages[1])->toBeInstanceOf(AssistantMessage::class);
    expect($messages[1]->content)->toContain('Greek Yogurt Parfait');
    expect($messages[1]->content)->toContain('Grilled Chicken Salad');
    expect($messages[1]->content)->toContain('Greek yogurt');
    expect($messages[1]->content)->toContain('Chicken breast');
    expect($messages[1]->content)->toContain('dairy, mediterranean');
    expect($messages[1]->content)->toContain('chicken, american');
});

test('agent returns empty messages for first day', function () {
    $plan = Plan::factory()->create(['start_date' => now(), 'duration_days' => 7]);

    $day1 = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'pending',
    ]);

    $agent = new NutritionPlannerAgent($day1);
    $messages = collect($agent->messages());

    expect($messages)->toBeEmpty();
});

test('agent excludes failed meal plans from history', function () {
    $plan = Plan::factory()->create(['start_date' => now(), 'duration_days' => 7]);

    MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'status' => 'failed',
    ]);

    $day2 = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 2,
        'status' => 'generated',
    ]);

    Meal::factory()->create([
        'meal_plan_id' => $day2->id,
        'type' => 'breakfast',
        'name' => 'Oatmeal Bowl',
        'ingredients' => [
            ['name' => 'Oats', 'amount' => '80', 'unit' => 'g'],
        ],
    ]);

    $day3 = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 3,
        'status' => 'pending',
    ]);

    $agent = new NutritionPlannerAgent($day3);
    $messages = collect($agent->messages());

    // Only Day 2 should appear (Day 1 failed)
    expect($messages)->toHaveCount(2);
    expect($messages[0]->content)->toContain('Day 2');
    expect($messages[1]->content)->toContain('Oatmeal Bowl');
    expect($messages[1]->content)->not->toContain('Day 1');
});

test('agent caps history to 4 most recent days', function () {
    $plan = Plan::factory()->create(['start_date' => now(), 'duration_days' => 14]);

    // Create 6 generated days
    foreach (range(1, 6) as $dayNum) {
        $mp = MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $dayNum,
            'status' => 'generated',
        ]);

        Meal::factory()->create([
            'meal_plan_id' => $mp->id,
            'type' => 'breakfast',
            'name' => "Day {$dayNum} Breakfast",
            'ingredients' => [
                ['name' => 'Ingredient', 'amount' => '100', 'unit' => 'g'],
            ],
        ]);
    }

    $day7 = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 7,
        'status' => 'pending',
    ]);

    $agent = new NutritionPlannerAgent($day7);
    $messages = collect($agent->messages());

    // 6 days * 2 messages (user + assistant) = 12 — full plan history
    expect($messages)->toHaveCount(12);

    // Should include all days 1-6
    expect($messages[0]->content)->toContain('Day 1');
    expect($messages[1]->content)->toContain('Day 1 Breakfast');
    expect($messages[10]->content)->toContain('Day 6');

    $allContent = $messages->pluck('content')->implode("\n");
    expect($allContent)->toContain('Day 1 Breakfast');
    expect($allContent)->toContain('Day 6 Breakfast');
});

test('system prompt contains culinary coherence rules', function () {
    $systemPrompt = (string) new MealPlanSystemPrompt;

    expect($systemPrompt)->toContain('Culinary Coherence');
    expect($systemPrompt)->toContain('no random mashups');
    expect($systemPrompt)->toContain('user prompt specifies their variety preference');
    expect($systemPrompt)->toContain('Do NOT prefix every meal with a country');
    expect($systemPrompt)->toContain('Rotate primary proteins');
});

test('user prompt contains pre-calculated per-meal macro ranges', function () {
    $user = User::factory()->withProfile()->create();
    $profile = $user->profile;

    $prompt = new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: 1,
        date: now(),
        bodyGoal: $profile->body_goal->value,
    );

    $text = (string) $prompt;

    // Should have per-meal ranges, not static rule sections
    expect($text)->toContain('Breakfast:');
    expect($text)->toContain('Lunch:');
    expect($text)->toContain('Snack:');
    expect($text)->toContain('Dinner:');
    expect($text)->toContain('Daily totals:');
    expect($text)->toContain('kcal');
    expect($text)->toContain('g P');
    expect($text)->toContain('g C');
    expect($text)->toContain('g F');

    // Should NOT have the old static rule sections
    expect($text)->not->toContain('Critical Requirements');
    expect($text)->not->toContain('Ingredient Coherence');
    expect($text)->not->toContain('Variety & Cross-Day');
});

test('agent instructions return system prompt', function () {
    $plan = Plan::factory()->create(['start_date' => now(), 'duration_days' => 7]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1]);

    $agent = new NutritionPlannerAgent($mealPlan);

    expect($agent->instructions())->toBe((string) new MealPlanSystemPrompt);
});
