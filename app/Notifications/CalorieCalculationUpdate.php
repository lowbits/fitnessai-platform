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
            //
        ];
    }
}
