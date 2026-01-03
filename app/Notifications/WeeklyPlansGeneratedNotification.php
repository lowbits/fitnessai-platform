<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return [ExpoChannel::class];
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
}

