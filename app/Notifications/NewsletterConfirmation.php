<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewsletterConfirmation extends Notification
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(NewsletterSubscriber $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?: app()->getLocale();

        $url = URL::temporarySignedRoute(
            'newsletter.confirm',
            now()->addDays(7),
            ['subscriber' => $notifiable->getKey(), 'locale' => $locale],
        );

        return (new MailMessage)
            ->subject(__('newsletter.confirm_email.subject', [], $locale))
            ->greeting(__('newsletter.confirm_email.greeting', [], $locale))
            ->line(__('newsletter.confirm_email.intro', [], $locale))
            ->action(__('newsletter.confirm_email.action', [], $locale), $url)
            ->line(__('newsletter.confirm_email.ignore', [], $locale));
    }
}
