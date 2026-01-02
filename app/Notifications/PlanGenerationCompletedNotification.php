<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class PlanGenerationCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title('✅ Your Plan is Ready!')
            ->body('Your personalized fitness and meal plan has been generated.')
            ->data([
                'type' => 'plan_ready',
                'screen' => 'Home',
            ])
            ->channelId('plans')
            ->badge(1)
            ->priority('high');
    }
}

