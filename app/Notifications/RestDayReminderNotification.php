<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class RestDayReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $locale = $notifiable->locale ?? 'en';

        return ExpoMessage::create()
            ->title('🌟 ' . __('notifications.rest_day.title', [], $locale))
            ->body(__('notifications.rest_day.body', [], $locale))
            ->data([
                'type' => 'rest_day_reminder',
                'screen' => 'Home',
            ])
            ->channelId('workouts')
            ->priority('normal');
    }
}

