<?php

namespace App\Console\Commands;

use App\Models\BodyProgress;
use App\Models\User;
use App\Notifications\WeeklyCheckinNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendWeeklyCheckins extends Command
{
    protected $signature = 'notifications:weekly-checkin';
    protected $description = 'Weekly weigh-in nudge for users who have not logged body progress this week';

    private const REMINDER_HOUR = 10;

    public function handle(): int
    {
        $now = now();

        $users = User::whereHas('devices')->get();

        $sent = 0;

        foreach ($users as $user) {
            $local = $now->copy()->timezone($user->getTimezone());
            if ($local->dayOfWeek !== Carbon::SUNDAY || $local->hour !== self::REMINDER_HOUR) {
                continue;
            }

            // Skip if a body-progress entry already exists in the current (local) week.
            $weekStart = $local->copy()->startOfWeek()->utc();
            $loggedThisWeek = BodyProgress::where('user_id', $user->id)
                ->where('recorded_at', '>=', $weekStart)
                ->exists();
            if ($loggedThisWeek) {
                continue;
            }

            // Idempotent: one weigh-in nudge per user per ISO week.
            if (! Cache::add("weekly_checkin_sent:{$user->id}:{$local->format('oW')}", true, $now->copy()->addDays(7))) {
                continue;
            }

            $user->notify(new WeeklyCheckinNotification());
            $sent++;
        }

        $this->info("✅ Weekly check-ins sent: {$sent}");

        return self::SUCCESS;
    }
}
