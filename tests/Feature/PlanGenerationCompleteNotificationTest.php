<?php

use App\Models\Plan;
use App\Models\User;
use App\Notifications\PlanGenerationComplete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('plan generation complete email includes app promo content', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->withoutPassword()
        ->create([
            'locale' => 'en',
        ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $notification = new PlanGenerationComplete($plan, "MOCKED KEY");
    $mail = $notification->toMail($user);

    // Convert mail to array to inspect content
    $mailData = $mail->toArray();


    // Check that app promo content is included
    expect(implode(' ', $mailData['introLines']))->toContain('your personalized **'.config('plans.duration_days').'-day plan**');
    expect(implode(' ', $mailData['introLines']))->toContain('**Even better with the app:**');
    expect(implode(' ', $mailData['introLines']))->toContain('Track your workouts');
    expect(implode(' ', $mailData['introLines']))->toContain('log your meals');
});

test('plan generation complete email includes app promo in German', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->withoutPassword()
        ->create([
            'locale' => 'de'
        ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $notification = new PlanGenerationComplete($plan, "MOCKED KEY");
    $mail = $notification->toMail($user);

    // Convert mail to array to inspect content
    $mailData = $mail->toArray();


    // Check that app promo content is included in German
    expect(implode(' ', $mailData['introLines']))->toContain('dein personalisierter **'.config('plans.duration_days').'-Tage Plan** ist fertig!');
    expect(implode(' ', $mailData['introLines']))->toContain('**Noch besser mit der App:**');
    expect(implode(' ', $mailData['introLines']))->toContain('Track deine Workouts');
    expect(implode(' ', $mailData['introLines']))->toContain('log deine Mahlzeiten');
});

test('plan generation complete email includes app promo image', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->withoutPassword()
        ->create([
            'locale' => 'en',
        ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $notification = new PlanGenerationComplete($plan, "MOCKED KEY");
    $mail = $notification->toMail($user);

    // Convert mail to array to inspect content
    $mailData = $mail->toArray();

    // Check that app promo image is included
    $content = implode(' ', $mailData['introLines']);
    expect($content)->toContain('app-promo_en.png');
});

test('plan generation complete email includes password reset link when user has no password', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->withoutPassword()
        ->create([
            'locale' => 'en',
            'email_verified_at' => now(),
        ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    // Generate a token
    $token = Password::broker(config('fortify.passwords'))->createToken($user);

    $notification = new PlanGenerationComplete($plan, $token);
    $mail = $notification->toMail($user);

    // Convert mail to array to inspect content
    $mailData = $mail->toArray();

    // Check that step 2 and CTA are included
    expect($mailData['actionText'])->toBe('Try 7 days free →');
    expect($mailData['actionUrl'])->toContain('set-password');
    expect($mailData['actionUrl'])->toContain(urlencode($user->email));
});

test('plan generation complete email does not include password reset link when user has password', function () {
    Notification::fake();

    $user = User::factory()
        ->withProfile()
        ->create([
            'locale' => 'en',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    // Even if we provide a token, it should not be included
    $notification = new PlanGenerationComplete($plan, 'some-token');
    $mail = $notification->toMail($user);

    // Convert mail to array to inspect content
    $mailData = $mail->toArray();

    // Check that CTA is NOT included
    expect($mailData['actionText'] ?? null)->toBeNull();
    expect($mailData['actionUrl'] ?? null)->toBeNull();
});

