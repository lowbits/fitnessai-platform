<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class WorkoutCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $workoutName
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    /**
     * Get the Expo representation of the notification.
     */
    public function toExpo(object $notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title('🎉 Workout Completed!')
            ->body("Great job completing {$this->workoutName}!")
            ->data([
                'type' => 'workout_completed',
                'screen' => 'Progress',
            ])
            ->channelId('achievements')
            ->badge(1)
            ->priority('high');
    }
}

