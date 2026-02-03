<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PromoLinkUsed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $email,
        public ?string $utmSource = null,
        public ?string $utmMedium = null,
        public ?string $utmCampaign = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];

    }

    /**
     * Get the mail representation of the notification (optional).
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Promo Link Used')
            ->greeting('A user clicked an app promo link!')
            ->line('**User:** ' . $this->email)
            ->line('**UTM Source:** ' . ($this->utmSource ?? 'N/A'))
            ->line('**UTM Medium:** ' . ($this->utmMedium ?? 'N/A'))
            ->line('**UTM Campaign:** ' . ($this->utmCampaign ?? 'N/A'))
            ->line('The user is now setting their password.');
    }


}

