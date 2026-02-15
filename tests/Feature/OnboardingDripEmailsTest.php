<?php

use App\Enums\BodyGoal;
use App\Enums\TrainingPlace;
use App\Enums\UserSource;
use App\Jobs\SendOnboardingDripEmail;
use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Notifications\Onboarding\Email02CoachCheckin;
use App\Notifications\Onboarding\Email03LimitationAngle;
use App\Notifications\Onboarding\Email04SocialProof;
use App\Notifications\Onboarding\Email05LastChance;
use App\Notifications\PlanGenerationComplete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function createWebUserWithPlan(array $profileOverrides = [], string $locale = 'en'): array
{
    $user = User::factory()
        ->withProfile($profileOverrides)
        ->withoutPassword()
        ->create(['locale' => $locale]);

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $workoutPlan = WorkoutPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
        'workout_name' => 'Upper Body Push',
    ]);

    $mealPlan = MealPlan::factory()->create([
        'plan_id' => $plan->id,
        'day_number' => 1,
    ]);

    $meal = Meal::factory()->create([
        'meal_plan_id' => $mealPlan->id,
        'name' => 'Protein Oats with Berries',
    ]);

    return [$user, $plan, $workoutPlan, $mealPlan, $meal];
}

// --- Email 1: PlanGenerationComplete ---

test('email 1 subject includes personalized body goal in EN', function () {
    [$user, $plan] = createWebUserWithPlan(['body_goal' => BodyGoal::MUSCLE_GAIN]);

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['subject'])->toBe('Your Build Muscle plan is ready 💪');
});

test('email 1 subject includes personalized body goal in DE', function () {
    [$user, $plan] = createWebUserWithPlan(['body_goal' => BodyGoal::WEIGHT_LOSS], 'de');

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['subject'])->toBe('Dein Plan zum Abnehmen ist da 💪');
});

test('email 1 includes first workout name and first meal', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('Upper Body Push');
    expect($content)->toContain('Protein Oats with Berries');
});

test('email 1 includes disclaimer', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new PlanGenerationComplete($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['outroLines']);

    expect($content)->toContain('AI-generated plan');
});

// --- Email 2: Coach Check-in ---

test('email 2 references first workout name', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new Email02CoachCheckin($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('Upper Body Push');
});

test('email 2 has correct subject in EN', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new Email02CoachCheckin($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['subject'])->toContain('Quick tip for your first workout');
});

test('email 2 has correct subject in DE', function () {
    [$user, $plan] = createWebUserWithPlan([], 'de');

    $notification = new Email02CoachCheckin($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['subject'])->toContain('Kurz zu deinem ersten Workout');
});

test('email 2 soft CTA links to download-app with UTM params', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new Email02CoachCheckin($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('fytrr.de/app');
    expect($content)->toContain('/app');
    expect($content)->toContain('utm_source=email');
    expect($content)->toContain('utm_medium=onboarding');
    expect($content)->toContain('utm_campaign=onboarding_02');
});

// --- Email 3: Limitation Angle ---

test('email 3 picks gym variant for gym training place', function () {
    [$user, $plan] = createWebUserWithPlan(['training_place' => TrainingPlace::GYM]);

    $notification = new Email03LimitationAngle($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('leg press');
});

test('email 3 picks home variant for home training place', function () {
    [$user, $plan] = createWebUserWithPlan(['training_place' => TrainingPlace::HOME]);

    $notification = new Email03LimitationAngle($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('dumbbells');
});

test('email 3 picks outdoor variant for outdoor training place', function () {
    [$user, $plan] = createWebUserWithPlan(['training_place' => TrainingPlace::OUTDOOR]);

    $notification = new Email03LimitationAngle($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('raining');
});

test('email 3 falls back to gym variant for NO_PREFERENCE', function () {
    [$user, $plan] = createWebUserWithPlan(['training_place' => TrainingPlace::NO_PREFERENCE]);

    $notification = new Email03LimitationAngle($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('leg press');
});

test('email 3 CTA links to download-app with UTM params', function () {
    [$user, $plan] = createWebUserWithPlan(['training_place' => TrainingPlace::GYM]);

    $notification = new Email03LimitationAngle($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['actionUrl'])->toContain('/app');
    expect($mailData['actionUrl'])->toContain('signature=');
    expect($mailData['actionUrl'])->toContain('utm_source=email');
    expect($mailData['actionUrl'])->toContain('utm_medium=onboarding');
    expect($mailData['actionUrl'])->toContain('utm_campaign=onboarding_03');
});

test('email 3 includes body goal in subject', function () {
    [$user, $plan] = createWebUserWithPlan([
        'body_goal' => BodyGoal::MUSCLE_GAIN,
        'training_place' => TrainingPlace::GYM,
    ]);

    $notification = new Email03LimitationAngle($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['subject'])->toContain('Build Muscle');
});

// --- Email 4: Social Proof ---

test('email 4 CTA links to download-app with UTM params', function () {
    [$user, $plan] = createWebUserWithPlan(['body_goal' => BodyGoal::WEIGHT_LOSS]);

    $notification = new Email04SocialProof($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['actionUrl'])->toContain('/app');
    expect($mailData['actionUrl'])->toContain('signature=');
    expect($mailData['actionUrl'])->toContain('utm_source=email');
    expect($mailData['actionUrl'])->toContain('utm_medium=onboarding');
    expect($mailData['actionUrl'])->toContain('utm_campaign=onboarding_04');
});

test('email 4 includes body goal in testimonial context', function () {
    [$user, $plan] = createWebUserWithPlan(['body_goal' => BodyGoal::WEIGHT_LOSS]);

    $notification = new Email04SocialProof($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('Lose Weight');
    expect($content)->toContain('Benedikt');
});

test('email 4 has correct DE content', function () {
    [$user, $plan] = createWebUserWithPlan(['body_goal' => BodyGoal::MUSCLE_GAIN], 'de');

    $notification = new Email04SocialProof($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('Muskelaufbau');
    expect($content)->toContain('Benedikt');
});

// --- Email 5: Last Chance ---

test('email 5 CTA links to download-app with UTM params', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new Email05LastChance($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();

    expect($mailData['actionUrl'])->toContain('/app');
    expect($mailData['actionUrl'])->toContain('signature=');
    expect($mailData['actionUrl'])->toContain('utm_source=email');
    expect($mailData['actionUrl'])->toContain('utm_medium=onboarding');
    expect($mailData['actionUrl'])->toContain('utm_campaign=onboarding_05');
});

test('email 5 includes training sessions per week', function () {
    [$user, $plan] = createWebUserWithPlan(['training_sessions_per_week' => 4]);

    $notification = new Email05LastChance($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['introLines']);

    expect($content)->toContain('4');
});

test('email 5 includes last email message', function () {
    [$user, $plan] = createWebUserWithPlan();

    $notification = new Email05LastChance($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $content = implode(' ', $mailData['outroLines']);

    expect($content)->toContain('last email');
});

test('email 5 has correct DE content', function () {
    [$user, $plan] = createWebUserWithPlan(['training_sessions_per_week' => 3], 'de');

    $notification = new Email05LastChance($plan);
    $mail = $notification->toMail($user);
    $mailData = $mail->toArray();
    $introContent = implode(' ', $mailData['introLines']);
    $outroContent = implode(' ', $mailData['outroLines']);

    expect($introContent)->toContain('3');
    expect($outroContent)->toContain('letzte Mail');
});

// --- Job: SendOnboardingDripEmail ---

test('drip job skips sending when user has password', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->create(['password' => bcrypt('password123')]);

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $job = new SendOnboardingDripEmail($user, $plan, step: 2);
    $job->handle();

    Notification::assertNothingSent();
});

test('drip job sends email 2 for web user without password', function () {
    Notification::fake();

    [$user, $plan] = createWebUserWithPlan();

    $job = new SendOnboardingDripEmail($user, $plan, step: 2);
    $job->handle();

    Notification::assertSentTo($user, Email02CoachCheckin::class);
});

test('drip job sends email 3 for web user without password', function () {
    Notification::fake();

    [$user, $plan] = createWebUserWithPlan();

    $job = new SendOnboardingDripEmail($user, $plan, step: 3);
    $job->handle();

    Notification::assertSentTo($user, Email03LimitationAngle::class);
});

test('drip job sends email 4 for web user without password', function () {
    Notification::fake();

    [$user, $plan] = createWebUserWithPlan();

    $job = new SendOnboardingDripEmail($user, $plan, step: 4);
    $job->handle();

    Notification::assertSentTo($user, Email04SocialProof::class);
});

test('drip job sends email 5 for web user without password', function () {
    Notification::fake();

    [$user, $plan] = createWebUserWithPlan();

    $job = new SendOnboardingDripEmail($user, $plan, step: 5);
    $job->handle();

    Notification::assertSentTo($user, Email05LastChance::class);
});

// --- CheckCompletedPlans dispatch ---

test('check completed plans dispatches 4 drip jobs for web user', function () {
    Bus::fake([SendOnboardingDripEmail::class]);
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->withoutPassword()
        ->create(['email_verified_at' => now()]);

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $totalDays = (int) config('plans.duration_days');
    for ($i = 1; $i <= $totalDays; $i++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $i,
            'status' => 'generated',
        ]);
        $mealPlan = MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $i,
            'status' => 'generated',
        ]);
        Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);
    }

    $this->artisan('plans:check-completed')->assertSuccessful();

    Bus::assertDispatched(SendOnboardingDripEmail::class, 4);

    Bus::assertDispatched(fn (SendOnboardingDripEmail $job) => $job->step === 2);
    Bus::assertDispatched(fn (SendOnboardingDripEmail $job) => $job->step === 3);
    Bus::assertDispatched(fn (SendOnboardingDripEmail $job) => $job->step === 4);
    Bus::assertDispatched(fn (SendOnboardingDripEmail $job) => $job->step === 5);
});

test('check completed plans does not dispatch drip jobs for mobile user', function () {
    Bus::fake([SendOnboardingDripEmail::class]);
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->create([
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'source' => UserSource::MOBILE_APPLE,
        ]);

    $plan = Plan::factory()->active()->create(['user_id' => $user->id]);

    $totalDays = (int) config('plans.duration_days');
    for ($i = 1; $i <= $totalDays; $i++) {
        WorkoutPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $i,
            'status' => 'generated',
        ]);
        $mealPlan = MealPlan::factory()->create([
            'plan_id' => $plan->id,
            'day_number' => $i,
            'status' => 'generated',
        ]);
        Meal::factory()->create(['meal_plan_id' => $mealPlan->id]);
    }

    $this->artisan('plans:check-completed')->assertSuccessful();

    Bus::assertNotDispatched(SendOnboardingDripEmail::class);
});
