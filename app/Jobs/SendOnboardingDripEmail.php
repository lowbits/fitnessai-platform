<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Models\User;
use App\Notifications\Onboarding\Email02CoachCheckin;
use App\Notifications\Onboarding\Email03LimitationAngle;
use App\Notifications\Onboarding\Email04SocialProof;
use App\Notifications\Onboarding\Email05LastChance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOnboardingDripEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Plan $plan,
        public int $step
    ) {}

    public function handle(): void
    {
        if (filled($this->user->password)) {
            return;
        }

        $notification = match ($this->step) {
            2 => new Email02CoachCheckin($this->plan),
            3 => new Email03LimitationAngle($this->plan),
            4 => new Email04SocialProof($this->plan),
            5 => new Email05LastChance($this->plan),
            default => null,
        };

        if ($notification) {
            $this->user->notify($notification);
        }
    }
}
