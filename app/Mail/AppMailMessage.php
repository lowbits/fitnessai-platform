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

    /**
     * Tag every email-internal link (logo, badges, social) with this campaign
     * so clicks can be attributed back to this specific email.
     */
    public function campaign(string $campaign): static
    {
        $this->viewData['emailCampaign'] = $campaign;

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
