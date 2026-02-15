<?php

use App\Enums\BodyGoal;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Notifications\PlanGenerationComplete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function createUserWithCompletePlan(array $profileOverrides = [], string $locale = 'en', bool $withPassword = false): array
{
    $factory = User::factory()->withProfile($profileOverrides);

    if (! $withPassword) {
        $factory = $factory->withoutPassword();
    }

    $user = $factory->create(['locale' => $locale]);

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'workout_name' => 'Upper Body Push',
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
    ]);

    Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Protein Oats with Berries',
    ]);

    return [$user, $plan];
}

test('plan generation complete email includes personalized content in English', function () {
    Notification::fake();

    [$user, $plan] = createUserWithCompletePlan(['body_goal' => BodyGoal::MUSCLE_GAIN]);

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    $content = implode(' ', $mailData['introLines']);

    expect($mailData['subject'])->toContain('Build Muscle');
    expect($content)->toContain('Upper Body Push');
    expect($content)->toContain('Protein Oats with Berries');
});

test('plan generation complete email includes personalized content in German', function () {
    Notification::fake();

    [$user, $plan] = createUserWithCompletePlan(['body_goal' => BodyGoal::WEIGHT_LOSS], 'de');

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['subject'])->toContain('Abnehmen');
    expect(implode(' ', $mailData['introLines']))->toContain('Upper Body Push');
});

test('plan generation complete email includes app pitch for web user', function () {
    Notification::fake();

    [$user, $plan] = createUserWithCompletePlan();

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('fytrr app');
    expect($content)->toContain('adapts automatically');
});

test('plan generation complete email has download-app CTA with UTM params for web user', function () {
    Notification::fake();

    [$user, $plan] = createUserWithCompletePlan();

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['actionText'])->toBe('7 days free →');
    expect($mailData['actionUrl'])->toContain('/app');
    expect($mailData['actionUrl'])->toContain('signature=');
    expect($mailData['actionUrl'])->toContain('utm_source=email');
    expect($mailData['actionUrl'])->toContain('utm_medium=notification');
    expect($mailData['actionUrl'])->toContain('utm_campaign=plan_ready');
});

test('plan generation complete email does not include app CTA for mobile user', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->create([
            'locale' => 'en',
            'source' => \App\Enums\UserSource::MOBILE_APPLE,
        ]);

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    WorkoutPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1]);
    $mealPlan = MealPlan::factory()->create(['plan_id' => $plan->id, 'day_number' => 1]);
    Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['actionText'])->toBe('View plan →');
});
