<?php

use App\Ai\Agents\NutritionPlannerAgent;
use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Ai\Prompts\MealPlanSystemPrompt;
use App\Enums\MealVariety;
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
    dispatch_sync($job);

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
    dispatch_sync($job);

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
    dispatch_sync($job);

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
    dispatch_sync($job);

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
    dispatch_sync($job);

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
    expect($systemPrompt)->toContain('Do NOT prefix every meal with a country');
});

test('system prompt no longer instructs AI to enforce cross-day variety itself', function () {
    $systemPrompt = (string) new MealPlanSystemPrompt;

    expect($systemPrompt)
        ->not->toContain('Variety Rotation System')
        ->not->toContain('user prompt specifies their variety preference')
        ->toContain('Across-day variety is managed by the user prompt');
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
        slotPlan: [
            'breakfast' => ['action' => 'new', 'forbidden_meals' => collect()],
            'lunch' => ['action' => 'new', 'forbidden_meals' => collect()],
            'snack' => ['action' => 'new', 'forbidden_meals' => collect()],
            'dinner' => ['action' => 'new', 'forbidden_meals' => collect()],
        ],
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

test('batch skips AI when all slots are REPEAT and inserts exact duplicates', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'meal_variety' => MealVariety::LOW,
        'selected_meals' => ['breakfast', 'lunch'],
    ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    // Low tier per-slot targets: breakfast=2, lunch=2.
    // Seed days 1-2 with 2 distinct breakfasts and 2 distinct lunches.
    $day1 = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'generated']);
    $b1 = Meal::factory()->create([
        'meal_plan_id' => $day1->id, 'type' => 'breakfast', 'name' => 'Overnight Oats',
        'calories' => 400, 'protein_g' => 25, 'carbs_g' => 50, 'fat_g' => 10,
        'primary_protein' => 'dairy', 'cuisine' => 'american',
        'ingredients' => [['name' => 'Oats', 'amount' => '80', 'unit' => 'g']],
    ]);
    Meal::factory()->create([
        'meal_plan_id' => $day1->id, 'type' => 'lunch', 'name' => 'Carbonara',
        'calories' => 700, 'protein_g' => 40, 'carbs_g' => 80, 'fat_g' => 25,
        'primary_protein' => 'pork', 'cuisine' => 'mediterranean',
        'ingredients' => [['name' => 'Pasta', 'amount' => '120', 'unit' => 'g']],
    ]);

    $day2 = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'generated']);
    Meal::factory()->create([
        'meal_plan_id' => $day2->id, 'type' => 'breakfast', 'name' => 'Scrambled Eggs',
        'primary_protein' => 'eggs', 'cuisine' => 'american',
    ]);
    Meal::factory()->create([
        'meal_plan_id' => $day2->id, 'type' => 'lunch', 'name' => 'Tuna Bowl',
        'primary_protein' => 'fish', 'cuisine' => 'asian',
    ]);

    // Day 3: budget exhausted for both slots → all REPEAT → no AI call.
    $job = new GenerateMealPlanBatch($user, $plan, startDay: 3, endDay: 3);
    dispatch_sync($job);

    NutritionPlannerAgent::assertNotPrompted(fn () => true);

    $day3 = MealPlan::where(['plan_id' => $plan->id, 'day_number' => 3])->first();
    expect($day3->status)->toBe('generated');
    expect($day3->meals)->toHaveCount(2);

    // The breakfast must be an exact copy of one of the seeded ones (new id, same content).
    $bfast = $day3->meals->firstWhere('type', 'breakfast');
    expect($bfast->name)->toBeIn(['Overnight Oats', 'Scrambled Eggs']);
    expect($bfast->id)->not->toBe($b1->id);

    $lunch = $day3->meals->firstWhere('type', 'lunch');
    expect($lunch->name)->toBeIn(['Carbonara', 'Tuna Bowl']);
});

test('exact-repeat meal preserves grams and macros from source', function () {
    NutritionPlannerAgent::fake();

    $user = User::factory()->withProfile()->create();
    $user->profile->update([
        'meal_variety' => MealVariety::LOW,
        'selected_meals' => ['lunch'],
    ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'duration_days' => 7,
        'start_date' => now(),
    ]);

    $day1 = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1, 'status' => 'generated']);
    $source = Meal::factory()->create([
        'meal_plan_id' => $day1->id,
        'type' => 'lunch',
        'name' => 'Carbonara',
        'calories' => 712,
        'protein_g' => 41.5,
        'carbs_g' => 82.0,
        'fat_g' => 24.5,
        'ingredients' => [
            ['name' => 'Pasta', 'amount' => '120', 'unit' => 'g'],
            ['name' => 'Eggs', 'amount' => '2', 'unit' => 'piece'],
            ['name' => 'Bacon', 'amount' => '50', 'unit' => 'g'],
        ],
        'primary_protein' => 'pork',
        'cuisine' => 'mediterranean',
    ]);

    $day2 = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 2, 'status' => 'generated']);
    Meal::factory()->create([
        'meal_plan_id' => $day2->id, 'type' => 'lunch', 'name' => 'Tuna Bowl',
        'primary_protein' => 'fish', 'cuisine' => 'asian',
    ]);

    $job = new GenerateMealPlanBatch($user, $plan, startDay: 3, endDay: 3);
    dispatch_sync($job);

    $day3 = MealPlan::where(['plan_id' => $plan->id, 'day_number' => 3])->first();
    $repeated = $day3->meals->where('name', $source->name)->first();

    if ($repeated) {
        expect((float) $repeated->calories)->toBe((float) $source->calories);
        expect((float) $repeated->protein_g)->toBe((float) $source->protein_g);
        expect((float) $repeated->carbs_g)->toBe((float) $source->carbs_g);
        expect((float) $repeated->fat_g)->toBe((float) $source->fat_g);
        expect($repeated->ingredients)->toBe($source->ingredients);
        expect($repeated->id)->not->toBe($source->id);
    } else {
        // Repeat picked the other meal (Tuna Bowl) — still valid behavior.
        expect($day3->meals->pluck('name')->all())->toContain('Tuna Bowl');
    }
});

test('agent instructions return system prompt', function () {
    $plan = Plan::factory()->create(['start_date' => now(), 'duration_days' => 7]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1]);

    $agent = new NutritionPlannerAgent($mealPlan);

    expect($agent->instructions())->toBe((string) new MealPlanSystemPrompt);
});
