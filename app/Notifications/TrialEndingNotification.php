<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class TrialEndingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class, 'mail'];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return ExpoMessage::create()
            ->title(__('notifications.trial_reminder.title', [], $locale))
            ->body(__('notifications.trial_reminder.body', ['days' => $this->daysLeft], $locale))
            ->data([
                'type' => 'trial_reminder',
                'screen' => 'Subscription',
            ])
            ->channelId('default')
            ->badge(1)
            ->priority('high');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return (new MailMessage())
            ->subject(__('notifications.trial_reminder.email.subject', [], $locale))
            ->greeting(__('notifications.trial_reminder.email.greeting', ['name' => $notifiable->name], $locale))
            ->line(__('notifications.trial_reminder.email.intro', ['days' => $this->daysLeft], $locale))
            ->line(__('notifications.trial_reminder.email.body', [], $locale));
    }
}
