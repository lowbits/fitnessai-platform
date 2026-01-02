<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class DailyWorkoutReminderNotification extends Notification implements ShouldQueue
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
        $locale = $notifiable->locale ?? 'en';

        return ExpoMessage::create()
            ->title('💪 ' . __('notifications.workout_reminder.title', [], $locale))
            ->body(__('notifications.workout_reminder.body', ['workout' => $this->workoutName], $locale))
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

