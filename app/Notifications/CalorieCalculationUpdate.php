<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CalorieCalculationUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Unique identifier for this notification campaign
     */
    public const CAMPAIGN_ID = 'calorie_update_2026_02';

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->preferredLocale();
        app()->setLocale($locale);

        return (new MailMessage)
            ->subject(__('emails.calorie_update.subject'))
            ->greeting(__('emails.calorie_update.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.calorie_update.intro'))
            ->line('')
            ->line(__('emails.calorie_update.what_changed_title'))
            ->line(__('emails.calorie_update.what_changed_text'))
            ->line('')
            ->line(__('emails.calorie_update.current_plan_title'))
            ->line(__('emails.calorie_update.current_plan_text'))
            ->line('')
            ->line(__('emails.calorie_update.why_title'))
            ->line(__('emails.calorie_update.why_text'))
            ->line('')
            ->line(__('emails.calorie_update.support'))
            ->line('')
            ->line(__('emails.calorie_update.closing'))
            ->salutation(__('emails.calorie_update.team'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'campaign_id' => self::CAMPAIGN_ID,
        ];
    }

    /**
     * Get the cache key for tracking if user has received this notification
     */
    public static function getCacheKey(int $userId): string
    {
        return 'notification:sent:' . self::CAMPAIGN_ID . ':user:' . $userId;
    }
}
