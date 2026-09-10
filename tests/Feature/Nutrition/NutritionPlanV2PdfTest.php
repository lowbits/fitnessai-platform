<?php

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;

function renderNutritionV2Pdf(): string
{
    app()->setLocale('de');

    $user = User::factory()->create();
    UserProfile::factory()->for($user)->buildMuscle()->create();
    $plan = Plan::factory()->for($user)->create(['plan_name' => 'Test Plan', 'daily_calories' => 2200, 'duration_days' => 7]);
    $mealPlan = MealPlan::factory()->for($plan)->create(['status' => 'generated', 'day_number' => 1]);
    Meal::factory()->for($mealPlan)->create([
        'type' => 'breakfast',
        'name' => 'Rührei mit Putenbrust',
        'ingredients' => [
            ['name' => 'Eier', 'unit' => 'piece', 'amount' => '3'],
            ['name' => 'Salz', 'unit' => 'to_taste', 'amount' => ''],
        ],
        'instructions' => ['Eier verquirlen.', 'In der Pfanne braten.'],
        'allergens' => ['eggs'],
    ]);

    return view('pdf.nutrition_plan_v2', [
        'user' => $user,
        'plan' => $plan,
        'mealPlans' => $plan->mealPlans()->with('meals')->orderBy('day_number')->get(),
    ])->render();
}

it('renders the v2 nutrition template with brand fonts and meal content', function () {
    expect(renderNutritionV2Pdf())
        ->toContain('Space Grotesk')
        ->toContain('Ernährungsplan')
        ->toContain('Rührei mit Putenbrust')
        ->toContain('Zutaten')
        ->toContain('Zubereitung');
});

it('formats ingredient amounts and to-taste in the v2 template', function () {
    expect(renderNutritionV2Pdf())
        ->toContain('nach Geschmack')
        ->toContain('Eier');
});
