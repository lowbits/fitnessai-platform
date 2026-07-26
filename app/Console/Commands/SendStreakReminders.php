<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\StreakReminderNotification;
use App\Services\StreakService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendStreakReminders extends Command
{
    protected $signature = 'notifications:streak-reminders';
    protected $description = 'Evening nudge to users whose streak is at risk (nothing tracked today)';

    /** Send at this local hour (evening last-chance). */
    private const REMINDER_HOUR = 20;

    /** Only nudge once the streak is worth protecting. */
    private const MIN_STREAK = 2;

    public function __construct(
        private readonly StreakService $streakService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $now = now();

        $users = User::whereHas('devices')
            ->whereHas('plans', fn ($q) => $q->where('status', 'active'))
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $local = $now->copy()->timezone($user->getTimezone());
            if ($local->hour !== self::REMINDER_HOUR) {
                continue;
            }

            $today = $local->toDateString();
            $yesterday = $local->copy()->subDay()->toDateString();

            $streak = $this->streakService->for($user);

            // At risk = a streak worth keeping, alive through yesterday, nothing logged today.
            if ($streak['current'] < self::MIN_STREAK) {
                continue;
            }
            if ($streak['last_activity_on'] === $today) {
                continue; // already tracked today
            }
            if ($streak['last_activity_on'] !== $yesterday) {
                continue; // not actually at risk *today*
            }

            // Idempotent: one streak nudge per user per local day.
            if (! Cache::add("streak_reminder_sent:{$user->id}:{$today}", true, $now->copy()->addDay())) {
                continue;
            }

            $user->notify(new StreakReminderNotification($streak['current']));
            $sent++;
        }

        $this->info("✅ Streak reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
