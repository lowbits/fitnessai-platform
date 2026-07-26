<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check for completed plans every 2 minutes
Schedule::command('plans:check-completed')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// ===== WEEKLY PLAN GENERATION =====
// Generate next 7 days for users with active subscriptions
// Runs DAILY and checks each user's individual generation day
// (3-4 days after their plan start date = mid-week of their cycle)
Schedule::command('plans:generate-weekly')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();

// ===== WORKOUT REMINDERS =====
// Check every hour and send to users based on their learned time
// MVP: 18:00 for new users, 1h before last tracking for others
Schedule::command('notifications:workout-reminders')
    ->hourly()
    ->between('6:00', '23:00') // Only between 6 AM and 11 PM
    ->withoutOverlapping()
    ->runInBackground();

// ===== MEAL REMINDERS =====
// Send ONE daily meal reminder at lunch time (12:00)
// MVP: Focus on most important meal, avoid spam
Schedule::command('notifications:meal-reminders')
    ->dailyAt('12:00')
    ->withoutOverlapping()
    ->runInBackground();

// ===== TRIAL REMINDER =====
// Push + email nudge 2 days before the free trial ends (drives conversion).
Schedule::command('notifications:trial-reminders')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->runInBackground();

// ===== STREAK REMINDER =====
// Evening last-chance nudge when a user's streak is at risk (nothing tracked today).
// Runs hourly; the command matches each user's local 20:00.
Schedule::command('notifications:streak-reminders')
    ->hourly()
    ->between('18:00', '22:00')
    ->withoutOverlapping()
    ->runInBackground();

// ===== WEEKLY WEIGH-IN =====
// Weekly check-in for users who haven't logged body progress this week.
// Runs hourly; the command matches each user's local Sunday 10:00.
Schedule::command('notifications:weekly-checkin')
    ->hourly()
    ->between('8:00', '12:00')
    ->withoutOverlapping()
    ->runInBackground();
