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
        $locale = $notifiable->locale === 'de' ? 'de' : 'en';

        $url = URL::temporarySignedRoute(
            'newsletter.confirm',
            now()->addDays(7),
            ['subscriber' => $notifiable->getKey(), 'locale' => $locale],
        );

        if ($locale === 'de') {
            return (new MailMessage)
                ->subject('Bitte bestätige deine Newsletter-Anmeldung')
                ->greeting('Fast geschafft!')
                ->line('Bitte bestätige, dass du den fytrr Newsletter mit Fitness-Tipps, Trainingsideen und exklusiven Inhalten erhalten möchtest.')
                ->action('Anmeldung bestätigen', $url)
                ->line('Wenn du dich nicht angemeldet hast, kannst du diese E-Mail einfach ignorieren.');
        }

        return (new MailMessage)
            ->subject('Please confirm your newsletter subscription')
            ->greeting('Almost there!')
            ->line('Please confirm that you would like to receive the fytrr newsletter with fitness tips, workout ideas, and exclusive content.')
            ->action('Confirm subscription', $url)
            ->line('If you did not sign up, you can simply ignore this email.');
    }
}
