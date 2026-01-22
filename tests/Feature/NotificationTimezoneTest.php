<?php

use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutTracking;
use App\Services\WorkoutReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('new user gets reminder at 9am in their timezone', function () {
    $service = new WorkoutReminderService();

    $user = User::factory()->create(['locale' => 'de']);
    Plan::factory()->create([
        'user_id' => $user->id,
        'created_at' => Carbon::parse('2024-01-10'),
    ]);

    $currentTime = Carbon::parse('2024-01-15 08:00:00'); // 09:00 Berlin

    expect($service->calculateReminderHour($user, $currentTime))->toBe(9);
    expect($service->shouldSendReminderNow($user, $currentTime))->toBeTrue();
});

test('old user without tracking gets reminder at 9am', function () {
    $service = new WorkoutReminderService();

    $user = User::factory()->create(['locale' => 'de']);
    Plan::factory()->create([
        'user_id' => $user->id,
        'created_at' => Carbon::parse('2023-12-01'), // 45 days ago
    ]);

    $currentTime = Carbon::parse('2024-01-15 08:00:00'); // 09:00 Berlin

    expect($service->calculateReminderHour($user, $currentTime))->toBe(9);
    expect($service->shouldSendReminderNow($user, $currentTime))->toBeTrue();
});

test('user with tracking gets reminder 2 hours before workout time', function () {
    $service = new WorkoutReminderService();

    $user = User::factory()->create(['locale' => 'de']);
    $plan = Plan::factory()->create([
        'user_id' => $user->id,
        'created_at' => Carbon::parse('2023-12-01'),
    ]);

    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'started_at' => '2024-01-14 17:00:00',
    ]);

    $currentTime = Carbon::parse('2024-01-15 15:00:00'); // 16:00 Berlin

    expect($service->calculateReminderHour($user, $currentTime))->toBe(16); // 18 - 2
    expect($service->shouldSendReminderNow($user, $currentTime))->toBeTrue();
});

test('reminder hour is clamped between 6 and 23', function () {
    $service = new WorkoutReminderService();

    $user = User::factory()->create(['locale' => 'de']);
    Plan::factory()->create([
        'user_id' => $user->id,
        'created_at' => Carbon::parse('2023-12-01'),
    ]);

    // User worked out at 7am, reminder would be 5am -> clamped to 6am
    WorkoutTracking::factory()->create([
        'user_id' => $user->id,
        'started_at' => Carbon::parse('2024-01-14 07:00:00', 'Europe/Berlin'),
    ]);

    $currentTime = Carbon::parse('2024-01-15 05:00:00'); // 06:00 Berlin

    expect($service->calculateReminderHour($user, $currentTime))->toBe(6);
});

test('returns correct today date in user timezone', function () {
    $service = new WorkoutReminderService();

    $germanUser = User::factory()->create(['locale' => 'de']);
    $englishUser = User::factory()->create(['locale' => 'en']);

    $currentTime = Carbon::parse('2024-01-15 23:30:00'); // 00:30 next day in Berlin

    expect($service->getTodayDateForUser($germanUser, $currentTime))->toBe('2024-01-16');
    expect($service->getTodayDateForUser($englishUser, $currentTime))->toBe('2024-01-15');
});

test('does not send reminder at wrong hour', function () {
    $service = new WorkoutReminderService();

    $user = User::factory()->create(['locale' => 'de']);
    Plan::factory()->create([
        'user_id' => $user->id,
        'created_at' => Carbon::parse('2024-01-10'),
    ]);

    $currentTime = Carbon::parse('2024-01-15 07:00:00'); // 08:00 Berlin (not 9am)

    expect($service->shouldSendReminderNow($user, $currentTime))->toBeFalse();
});
