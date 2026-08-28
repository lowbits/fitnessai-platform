<?php

use App\Ai\Prompts\CreateMealPlanPrompt;
use App\Enums\CookingPreference;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Build a default slot plan: every slot is NEW with no forbidden meals
 * (the day-1 case). Tests that need REPEAT/forbidden override this.
 *
 * @param  list<string>|null  $selectedSlots
 */
function defaultSlotPlan(?array $selectedSlots = null): array
{
    $slots = $selectedSlots ?? ['breakfast', 'lunch', 'snack', 'dinner'];

    return collect($slots)
        ->mapWithKeys(fn (string $slot) => [$slot => ['action' => 'new', 'forbidden_meals' => collect()]])
        ->all();
}

function createPrompt(array $profileOverrides = [], ?Carbon $date = null, int $dayNumber = 1, ?array $slotPlan = null): string
{
    $profile = UserProfile::factory()->create($profileOverrides);
    $profile->load('user');

    $selected = $profile->selected_meals ?: ['breakfast', 'lunch', 'snack', 'dinner'];

    $prompt = new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: $dayNumber,
        date: $date ?? Carbon::parse('2026-06-09'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
        slotPlan: $slotPlan ?? defaultSlotPlan($selected),
    );

    return (string) $prompt;
}

test('prompt includes food dislikes', function () {
    $output = createPrompt(['food_dislikes' => ['pork', 'mushrooms']]);

    expect($output)->toContain('disliked ingredients: pork, mushrooms');
});

test('prompt excludes dislikes line when empty', function () {
    $output = createPrompt(['food_dislikes' => []]);

    expect($output)->not->toContain('NEVER use');
});

test('prompt includes cooking constraint for quick', function () {
    // Default date 2026-06-09 is a weekday → QUICK emits the WORKDAY constraint.
    $output = createPrompt(['cooking_preference' => CookingPreference::QUICK]);

    expect($output)->toContain('max 12min total prep+cook per meal');
});

test('prompt includes cooking constraint for elaborate', function () {
    $output = createPrompt(['cooking_preference' => CookingPreference::ELABORATE]);

    expect($output)->toContain('enjoys cooking');
});

test('prompt includes moderate cooking constraint for normal', function () {
    // NORMAL now emits its own (moderate) constraint; weekday → WORKDAY variant.
    $output = createPrompt(['cooking_preference' => CookingPreference::NORMAL]);

    expect($output)->toContain('max 20min total prep+cook per meal');
});

test('prompt generates only NEW slots', function () {
    $output = createPrompt(
        ['selected_meals' => ['breakfast', 'lunch', 'dinner']],
        slotPlan: defaultSlotPlan(['breakfast', 'lunch', 'dinner']),
    );

    expect($output)
        ->toContain('generate 3 meals: breakfast, lunch, dinner')
        ->not->toContain('Snack:');
});

test('prompt defaults to 4 meals when selected_meals is empty', function () {
    $output = createPrompt(['selected_meals' => null]);

    expect($output)->toContain('generate 4 meals: breakfast, lunch, snack, dinner');
});

test('prompt omits REPEAT slots from generate list', function () {
    $source = Meal::factory()->create([
        'type' => 'lunch',
        'name' => 'Carbonara',
        'primary_protein' => 'pork',
        'cuisine' => 'mediterranean',
    ]);

    $slotPlan = [
        'breakfast' => ['action' => 'new', 'forbidden_meals' => collect()],
        'lunch' => ['action' => 'repeat', 'repeat_from' => $source],
        'snack' => ['action' => 'new', 'forbidden_meals' => collect()],
        'dinner' => ['action' => 'new', 'forbidden_meals' => collect()],
    ];

    $output = createPrompt(slotPlan: $slotPlan);

    expect($output)
        ->toContain('generate 3 meals: breakfast, snack, dinner')
        ->not->toContain('Carbonara');
});

test('prompt lists ALL prior meals across slots (cross-slot twin guard)', function () {
    $forbidden = collect([
        new Meal([
            'name' => 'Spinat-Ricotta-Lasagne',
            'type' => 'dinner',
            'primary_protein' => 'dairy',
            'format' => 'bake',
            'hero_veg' => 'spinach',
        ]),
        new Meal([
            'name' => 'Tofu-Udon-Bowl',
            'type' => 'lunch',
            'primary_protein' => 'tofu',
            'format' => 'noodles',
            'hero_veg' => 'broccoli',
        ]),
    ]);

    $slotPlan = defaultSlotPlan();
    $slotPlan['lunch'] = ['action' => 'new', 'forbidden_meals' => $forbidden];

    $output = createPrompt(slotPlan: $slotPlan);

    expect($output)
        ->toContain('Prior meals this week (across ALL slots)')
        ->toContain('MUST differ on at least ONE of {primary_protein, format}')
        ->toContain('"Spinat-Ricotta-Lasagne" [slot: dinner, protein: dairy, format: bake]')
        ->toContain('"Tofu-Udon-Bowl" [slot: lunch, protein: tofu, format: noodles]')
        ->not->toContain('hero_veg: spinach')
        ->not->toContain('hero_veg: broccoli');
});

test('prompt omits forbidden section when no prior meals exist', function () {
    $output = createPrompt();

    expect($output)->not->toContain('MUST differ on at least ONE');
});

test('macro targets for NEW slots use natural per-day share, not renormalized over NEW only', function () {
    // Day 5 → MEAL_SPLITS index 0: B=0.275, L=0.325, S=0.125, D=0.275 (sum 1.0).
    // With 2 REPEAT slots (breakfast, snack) and 2 NEW (lunch, dinner), the
    // emitted lunch/dinner targets must reflect their natural shares of the
    // daily total — NOT inflated to fill 100% of the day. Bug fixed Day 5
    // had AI told lunch=2,146 kcal (= 0.325/0.60 × daily). Should be ~1,290.

    $source = Meal::factory()->create();

    $slotPlan = [
        'breakfast' => ['action' => 'repeat', 'repeat_from' => $source],
        'lunch' => ['action' => 'new', 'forbidden_meals' => collect()],
        'snack' => ['action' => 'repeat', 'repeat_from' => $source],
        'dinner' => ['action' => 'new', 'forbidden_meals' => collect()],
    ];

    // auto_fill off → no capping/flex, so the natural per-slot share is emitted verbatim.
    $profile = UserProfile::factory()->create(['selected_meals' => null, 'auto_fill_calories' => false]);
    $profile->load('user');

    $prompt = (string) new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: 5,
        date: Carbon::parse('2026-06-20'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
        slotPlan: $slotPlan,
    );

    $daily = $profile->getMetabolismData()['daily_calories'];

    // Lunch natural share = 0.325 → target kcal with a two-sided ±5% band.
    $lunchKcal = (int) round($daily * 0.325);
    $lunchMin = (int) round($lunchKcal * 0.95);
    $lunchMax = (int) round($lunchKcal * 1.05);
    expect($prompt)->toContain("Lunch: {$lunchKcal} kcal (min {$lunchMin} / max {$lunchMax})");

    // Dinner natural share = 0.275.
    $dinnerKcal = (int) round($daily * 0.275);
    $dinnerMin = (int) round($dinnerKcal * 0.95);
    $dinnerMax = (int) round($dinnerKcal * 1.05);
    expect($prompt)->toContain("Dinner: {$dinnerKcal} kcal (min {$dinnerMin} / max {$dinnerMax})");

    // Only NEW slots should appear in the macro table.
    expect($prompt)->not->toContain('Breakfast:');
    expect($prompt)->not->toContain('Snack:');

    // Sanity: prompted lunch + dinner together should be ~60% of daily total
    // (not 100% — which was the bug).
    expect($lunchMax + $dinnerMax)->toBeLessThan((int) round($daily * 0.7));
});

test('caps mains and asks the AI to fill the remainder, flex-first, on a high-calorie day', function () {
    $slotPlan = [
        'breakfast' => ['action' => 'new', 'forbidden_meals' => collect()],
        'lunch' => ['action' => 'new', 'forbidden_meals' => collect()],
        'dinner' => ['action' => 'new', 'forbidden_meals' => collect()],
    ];

    // auto_fill defaults on; a deterministically high-calorie persona forces a fill budget.
    $profile = UserProfile::factory()->create([
        'selected_meals' => ['breakfast', 'lunch', 'dinner'],
        'gender' => 'male',
        'weight_kg' => 120,
        'height_cm' => 200,
        'birthdate' => now()->subYears(25)->format('Y-m-d'),
        'activity_level' => 'hard_working',
        'training_sessions_per_week' => 7,
    ]);
    $profile->load('user');
    expect((int) $profile->getMetabolismData()['daily_calories'])->toBeGreaterThan(2700);

    $prompt = (string) new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: 5,
        date: Carbon::parse('2026-06-20'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
        slotPlan: $slotPlan,
    );

    expect($prompt)->toContain('Lunch: 800 kcal (min 760 / max 840)')
        ->toContain('FILL')
        ->toContain('flex" protein shakes');
});

test('macro targets renormalize over user-selected slots when user skipped one', function () {
    // User skipped snack → 3 slots. Renormalization redistributes the missing
    // 12.5% across the other 3. All 3 are NEW today → each one's emitted share
    // is its natural × (1 / 0.875).
    $slotPlan = [
        'breakfast' => ['action' => 'new', 'forbidden_meals' => collect()],
        'lunch' => ['action' => 'new', 'forbidden_meals' => collect()],
        'dinner' => ['action' => 'new', 'forbidden_meals' => collect()],
    ];

    // auto_fill off isolates the renormalization behaviour from capping/flex.
    $profile = UserProfile::factory()->create([
        'selected_meals' => ['breakfast', 'lunch', 'dinner'],
        'auto_fill_calories' => false,
    ]);
    $profile->load('user');

    $prompt = (string) new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: 5,
        date: Carbon::parse('2026-06-20'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
        slotPlan: $slotPlan,
    );

    $daily = $profile->getMetabolismData()['daily_calories'];

    // Renormalized lunch share = 0.325 / 0.875 → target kcal.
    $expectedLunch = (int) round($daily * (0.325 / 0.875));
    expect($prompt)->toContain("Lunch: {$expectedLunch} kcal");
    expect($prompt)->not->toContain('Snack:');
});

test('prompt no longer contains prose variety rules', function () {
    $output = createPrompt();

    expect($output)
        ->not->toContain('VARIETY RULE')
        ->not->toContain('MUST FOLLOW')
        ->not->toContain('unique recipes')
        ->not->toContain('BEFORE generating: count');
});

test('prompt includes favorite recipe signals', function () {
    $user = User::factory()->create();
    $profile = UserProfile::factory()->create(['user_id' => $user->id]);

    $recipes = Recipe::factory()->count(2)->create([
        'cuisine' => 'mediterranean',
        'primary_protein' => 'chicken',
    ]);
    $user->favoriteRecipes()->attach($recipes->pluck('id'));
    $user->load('favoriteRecipes');
    $profile->load('user');

    $prompt = new CreateMealPlanPrompt(
        profile: $profile,
        locale: 'en',
        dayNumber: 1,
        date: Carbon::parse('2026-06-09'),
        bodyGoal: $profile->body_goal->resolveCanonical()->value,
        slotPlan: defaultSlotPlan(),
    );

    expect((string) $prompt)
        ->toContain('Preferred cuisines: mediterranean')
        ->toContain('Preferred proteins: chicken');
});
