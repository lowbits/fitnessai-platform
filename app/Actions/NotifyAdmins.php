<?php

namespace App\Actions;

use Illuminate\Notifications\Notification;

class NotifyAdmins
{
    /**
     * Send a notification to all configured admin emails.
     */
    public function send(Notification $notification): void
    {
        $adminEmails = config('app.admin_emails');

        \Illuminate\Support\Facades\Notification::route('mail', $adminEmails)
            ->notify($notification);
    }
}
