<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class WeeklyCheckinNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return ExpoMessage::create()
            ->title(__('notifications.weekly_checkin.title', [], $locale))
            ->body(__('notifications.weekly_checkin.body', [], $locale))
            ->data([
                'type' => 'weekly_checkin',
                'screen' => 'BodyProgress',
            ])
            ->channelId('default')
            ->badge(1);
    }
}
