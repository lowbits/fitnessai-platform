<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class StreakReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $streak
    ) {}

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return ExpoMessage::create()
            ->title(__('notifications.streak_reminder.title', [], $locale))
            ->body(__('notifications.streak_reminder.body', ['count' => $this->streak], $locale))
            ->data([
                'type' => 'streak_reminder',
                'screen' => 'Home',
            ])
            ->channelId('default')
            ->badge(1)
            ->priority('high');
    }
}
