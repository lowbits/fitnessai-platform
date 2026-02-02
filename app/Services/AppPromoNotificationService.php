<?php

namespace App\Services;

use App\Jobs\SendAppPromoNotificationJob;
use App\Models\Plan;
use App\Models\User;

class AppPromoNotificationService
{
    /**
     * Send app promo notification to user if they don't have a password set.
     *
     * The notification is dispatched as a queued job. In production, the job is delayed
     * by 24 hours (unless $immediate is true), ensuring the password reset token is
     * generated at the time the email is actually sent, not when it's scheduled.
     *
     * @param User $user
     * @param Plan $plan
     * @param bool $immediate If true, send immediately. If false, apply 24h delay in production.
     * @return bool
     */
    public function sendToUser(User $user, Plan $plan, bool $immediate = false): bool
    {
        if (filled($user->password)) {
            return false;
        }

        // Dispatch job with delay in production (unless immediate)
        $job = new SendAppPromoNotificationJob($user, $plan);

        if (!$immediate && app()->environment('production')) {
            $job->delay(now()->addHours(24));
        }

        dispatch($job);

        return true;
    }
}

