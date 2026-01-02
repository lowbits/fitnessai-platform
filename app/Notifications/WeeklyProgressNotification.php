<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class WeeklyProgressNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $stats
    ) {}

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $workoutsCompleted = $this->stats['workouts_completed'] ?? 0;

        return ExpoMessage::create()
            ->title('📊 Weekly Progress')
            ->body("You completed {$workoutsCompleted} workouts this week. Keep it up!")
            ->data([
                'type' => 'weekly_summary',
                'stats' => $this->stats,
                'screen' => 'Progress',
            ])
            ->channelId('progress')
            ->badge(1);
    }
}

