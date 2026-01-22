<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class WorkoutReminderService
{
    public function shouldSendReminderNow(User $user, Carbon $currentTime): bool
    {
        $userHour = $currentTime->copy()->timezone($user->getTimezone())->hour;

        $reminderHour = $this->calculateReminderHour($user, $currentTime);

        return $userHour === $reminderHour;
    }

    public function calculateReminderHour(User $user, Carbon $currentTime): int
    {
        $firstPlan = $user->plans()->oldest()->first();
        $isNewUser = !$firstPlan || $firstPlan->created_at->gte($currentTime->copy()->subDays(14));

        $latestTracking = $user->workoutTrackings()
            ->whereNotNull('started_at')
            ->latest('started_at')
            ->first();

        // Default: 9 AM for new users or users without tracking
        if ($isNewUser || !$latestTracking) {
            return 9;
        }

        // Learned behavior: 2 hours before last workout
        $lastWorkoutHour = Carbon::parse($latestTracking->started_at)
            ->timezone($user->getTimezone())
            ->hour;

        return max(6, min(23, $lastWorkoutHour - 2));
    }

    public function getTodayDateForUser(User $user, Carbon $currentTime): string
    {
        return $currentTime->copy()->timezone($user->getTimezone())->toDateString();
    }
}
