<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailyWorkoutReminderNotification;
use App\Notifications\RestDayReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWorkoutReminders extends Command
{
    protected $signature = 'notifications:workout-reminders';
    protected $description = 'Send daily workout reminders to users with active plans';

    public function handle(): int
    {
        $currentHour = now()->hour;
        $this->info("Starting workout reminder notifications for hour {$currentHour}:00...");

        // Get users with active plans and devices
        $users = User::whereHas('devices')
            ->whereHas('plans', function ($query) {
                $query->where('status', 'active')
                    ->whereNotNull('generation_completed_at');
            })
            ->with(['plans' => function ($query) {
                $query->where('status', 'active')
                    ->latest()
                    ->limit(1);
            }])
            ->get();

        $sentCount = 0;
        $restDayCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $plan = $user->plans->first();

            if (!$plan) {
                continue;
            }

            // Determine when to send reminder for this user
            $shouldSendNow = $this->shouldSendReminderNow($user, $currentHour);

            if (!$shouldSendNow) {
                $skippedCount++;
                continue;
            }

            // Get today's workout
            $todayWorkout = $plan->workoutPlans()
                ->whereDate('date', today())
                ->where('status', 'generated')
                ->first();

            if (!$todayWorkout) {
                continue;
            }

            // Check if it's a rest day
            if ($todayWorkout->workout_type === 'rest') {
                $user->notify(new RestDayReminderNotification());
                $restDayCount++;
                $this->line("✅ Rest day reminder sent to user {$user->id} ({$user->email})");
            } else {
                $user->notify(new DailyWorkoutReminderNotification(
                    $todayWorkout->workout_name,
                    $todayWorkout->id
                ));
                $sentCount++;
                $this->line("✅ Workout reminder sent to user {$user->id} ({$user->email}) - {$todayWorkout->workout_name}");
            }
        }

        $this->info("✅ Workout reminders sent: {$sentCount}");
        $this->info("✅ Rest day reminders sent: {$restDayCount}");
        $this->info("⏭️  Skipped (wrong time): {$skippedCount}");
        $this->info('Done!');

        return Command::SUCCESS;
    }

    /**
     * Determine if reminder should be sent now for this user
     * MVP Logic:
     * - First 2 weeks OR no trackings: Send at 18:00
     * - Has trackings: Send 1 hour before average latest tracking time
     */
    private function shouldSendReminderNow(User $user, int $currentHour): bool
    {
        // Check if user is in first 2 weeks
        $firstPlan = $user->plans()->oldest()->first();
        $isNewUser = !$firstPlan || $firstPlan->created_at->diffInDays(now()) <= 14;

        // Get user's latest workout tracking (use started_at, not completed_at)
        $latestTracking = $user->workoutTrackings()
            ->whereNotNull('started_at')
            ->latest('started_at')
            ->first();

        // No tracking or new user: Default 18:00 (6 PM)
        if (!$latestTracking || $isNewUser) {
            $reminderHour = 18;
            $this->line("User {$user->id}: Using default time 18:00 (new user or no tracking)");
        } else {
            // Calculate 1 hour before their latest workout START time
            $lastWorkoutHour = Carbon::parse($latestTracking->started_at)->hour;
            $reminderHour = $lastWorkoutHour - 1;

            // Ensure it's between 6 AM and 11 PM
            $reminderHour = max(6, min(23, $reminderHour));

            $this->line("User {$user->id}: Learned time " . sprintf('%02d:00', $reminderHour) . " (1h before last workout START at {$lastWorkoutHour}:00)");
        }

        return $currentHour === $reminderHour;
    }
}
