<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class MealReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $mealType,
        private string $mealName,
        private int $mealId
    ) {}

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $locale = $notifiable->locale ?? 'en';

        $mealEmojis = [
            'breakfast' => '🌅',
            'lunch' => '🍽️',
            'snack' => '🥗',
            'dinner' => '🌙',
        ];

        $emoji = $mealEmojis[$this->mealType] ?? '🍴';

        return ExpoMessage::create()
            ->title($emoji . ' ' . __("notifications.meal_reminder.{$this->mealType}.title", [], $locale))
            ->body(__("notifications.meal_reminder.{$this->mealType}.body", ['meal' => $this->mealName], $locale))
            ->data([
                'type' => 'meal_reminder',
                'meal_type' => $this->mealType,
                'meal_id' => $this->mealId,
                'screen' => 'MealDetail',
            ])
            ->channelId('meals')
            ->badge(1)
            ->priority('normal');
    }
}

