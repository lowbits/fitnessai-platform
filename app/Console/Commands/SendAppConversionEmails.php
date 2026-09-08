<?php

namespace App\Console\Commands;

use App\Enums\UserSource;
use App\Models\User;
use App\Notifications\Onboarding\Email02CoachCheckin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendAppConversionEmails extends Command
{
    protected $signature = 'notifications:app-conversion';

    protected $description = 'Nudge web PDF users to try the app a couple of days after their free plan';

    /** Fire this many days after the user got their free PDF (email verified). */
    private const DAYS_AFTER_PDF = 2;

    public function handle(): int
    {
        $windowStart = now()->subDays(self::DAYS_AFTER_PDF)->startOfDay();
        $windowEnd = now()->subDays(self::DAYS_AFTER_PDF)->endOfDay();

        // Web users who got their PDF ~2 days ago and have not converted to the
        // app yet (no password set, no registered device).
        $users = User::query()
            ->where('source', UserSource::WEB)
            ->whereNull('password')
            ->whereNotNull('email_verified_at')
            ->whereBetween('email_verified_at', [$windowStart, $windowEnd])
            ->whereDoesntHave('devices')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $plan = $user->plans()->latest()->first();

            if (! $plan) {
                continue;
            }

            // Idempotent: at most one conversion email per user.
            if (! Cache::add("app_conversion_sent:{$user->id}", true, now()->addDays(30))) {
                continue;
            }

            $user->notify(new Email02CoachCheckin($plan));
            $sent++;
        }

        $this->info("✅ App conversion emails sent: {$sent}");

        return self::SUCCESS;
    }
}
