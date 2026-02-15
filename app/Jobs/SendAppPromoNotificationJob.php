<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Models\User;
use App\Notifications\AppPromoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

/**
 * @deprecated Replaced by App\Jobs\SendOnboardingDripEmail.
 */
class SendAppPromoNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Plan $plan
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Guard: Skip if user already has a password
        if (filled($this->user->password)) {
            return;
        }

        // Generate token and send notification at execution time
        Password::broker(config('fortify.passwords'))->sendResetLink(
            ['email' => $this->user->email],
            function ($user, $token) {
                // Double-check password hasn't been set since job was queued
                if (filled($user->password)) {
                    return Password::INVALID_USER;
                }

                $notification = new AppPromoNotification($this->plan, $token);
                $user->notify($notification);

                return Password::RESET_LINK_SENT;
            }
        );
    }
}
