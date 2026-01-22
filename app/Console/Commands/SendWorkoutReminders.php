<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailyWorkoutReminderNotification;
use App\Notifications\RestDayReminderNotification;
use App\Services\WorkoutReminderService;
use Illuminate\Console\Command;

class SendWorkoutReminders extends Command
{
    protected $signature = 'notifications:workout-reminders';
    protected $description = 'Send daily workout reminders to users with active plans';

    public function __construct(
        private readonly WorkoutReminderService $reminderService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $currentTime = now();
        $this->info("Starting workout reminder notifications for {$currentTime}...");

        $users = User::whereHas('devices')
            ->whereHas('plans', fn($q) => $q->where('status', 'active')->whereNotNull('generation_completed_at'))
            ->get();

        $sentCount = 0;
        $restDayCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $plan = $user->plans()
                ->where('status', 'active')
                ->whereNotNull('generation_completed_at')
                ->latest()
                ->first();

            if (!$plan) {
                continue;
            }

            if (!$this->reminderService->shouldSendReminderNow($user, $currentTime)) {
                $skippedCount++;
                continue;
            }

            $userToday = $this->reminderService->getTodayDateForUser($user, $currentTime);

            $todayWorkout = $plan->workoutPlans()
                ->whereDate('date', $userToday)
                ->where('status', 'generated')
                ->first();

            if (!$todayWorkout) {
                continue;
            }

            if ($todayWorkout->workout_type === 'rest') {
                $user->notify(new RestDayReminderNotification());
                $restDayCount++;
            } else {
                $user->notify(new DailyWorkoutReminderNotification($todayWorkout->workout_name, $todayWorkout->id));
                $sentCount++;
            }
        }

        $this->info("✅ Sent: {$sentCount}, Rest days: {$restDayCount}, Skipped: {$skippedCount}");
        return Command::SUCCESS;
    }
}
