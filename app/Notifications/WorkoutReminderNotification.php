<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class WorkoutReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $workoutName,
        private int $workoutId
    ) {}

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title('🏋️ Workout Reminder')
            ->body("Time for your workout: {$this->workoutName}")
            ->data([
                'type' => 'workout_reminder',
                'workout_id' => $this->workoutId,
                'screen' => 'WorkoutDetail',
            ])
            ->channelId('workouts')
            ->badge(1)
            ->priority('high');
    }
}

