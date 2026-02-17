<?php

namespace App\Mail;

use Illuminate\Notifications\Messages\MailMessage;

class AppMailMessage extends MailMessage
{
    public function previewText(string $text): static
    {
        $this->viewData['previewText'] = $text;

        return $this;
    }

    public function useAdminMailer(): static
    {
        if (app()->isProduction()) {
            $this->mailer('admin_smtp');
        }
        return $this;
    }
}
