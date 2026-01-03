<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class WeeklyPlansGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $startDate,
        private string $endDate
    ) {}

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class, 'mail'];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return ExpoMessage::create()
            ->title('🎯 ' . __('notifications.weekly_plans_generated.title', [], $locale))
            ->body(__('notifications.weekly_plans_generated.body', [], $locale))
            ->data([
                'type' => 'weekly_plans_generated',
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'screen' => 'Plans',
            ])
            ->channelId('plans')
            ->badge(1)
            ->priority('default');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return (new MailMessage)
            ->subject(__('notifications.weekly_plans_generated.email.subject', [], $locale))
            ->greeting(__('notifications.weekly_plans_generated.email.greeting', ['name' => $notifiable->name], $locale))
            ->line(__('notifications.weekly_plans_generated.email.intro', [], $locale))
            ->line(__('notifications.weekly_plans_generated.email.details', [], $locale))
            ->action(__('notifications.weekly_plans_generated.email.action', [], $locale), 'fytrr://(tabs)')
            ->line(__('notifications.weekly_plans_generated.email.closing', [], $locale));
    }
}

