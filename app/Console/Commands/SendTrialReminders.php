<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TrialEndingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendTrialReminders extends Command
{
    protected $signature = 'notifications:trial-reminders';
    protected $description = 'Remind trial users (push + email) a couple of days before their free trial ends';

    /** Fire when the trial has this many days left. */
    private const DAYS_BEFORE_END = 2;

    public function handle(): int
    {
        $target = now()->addDays(self::DAYS_BEFORE_END);

        $users = User::whereHas('devices')
            ->whereHas('subscriptions', fn ($q) => $q
                ->where('status', 'trial')
                ->whereBetween('trial_ended_at', [$target->copy()->startOfDay(), $target->copy()->endOfDay()]))
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            // Idempotent: at most one reminder per user per trial window, even on re-runs.
            $guardKey = "trial_reminder_sent:{$user->id}";
            if (! Cache::add($guardKey, true, now()->addDays(self::DAYS_BEFORE_END + 1))) {
                continue;
            }

            $user->notify(new TrialEndingNotification(self::DAYS_BEFORE_END));
            $sent++;
        }

        $this->info("✅ Trial reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
