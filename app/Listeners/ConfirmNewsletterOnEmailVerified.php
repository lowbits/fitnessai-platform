<?php

namespace App\Listeners;

use App\Enums\NewsletterStatus;
use App\Events\EmailVerified;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;

class ConfirmNewsletterOnEmailVerified
{
    public function __construct(private readonly NewsletterService $newsletter) {}

    public function handle(EmailVerified $event): void
    {
        $subscriber = NewsletterSubscriber::where('email', $event->user->email)
            ->where('status', NewsletterStatus::Pending)
            ->first();

        if ($subscriber) {
            $this->newsletter->confirm($subscriber);
        }
    }
}
