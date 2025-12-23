<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanReadyForDelivery extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Plan $plan,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];

    }

    /**
     * Get the mail representation of the notification (optional).
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plan ready for delivery')
            ->greeting('New Plan was generated')
            ->line('A new plan is ready to be delivered.')
            ->line('**Plan Details:**')
            ->line('Name: ' . $this->plan->plan_name)
            ->line('User: ' . $this->plan->user->name)
            ->line('Email: ' . $this->plan->user->email)
            ->line('Duration Days: ' . ($this->plan->duration_days ?? 'N/A'))
            ->line('Daily Calories: ' . ($this->plan->daily_calories ?? 'N/A'))
            ->line('Proteins: ' . ($this->plan->daily_protein_g ?? 'N/A'))
            ->line('Carbs: ' . ($this->plan->daily_carbs_g ?? 'N/A'))
            ->line('Fats: ' . ($this->plan->daily_fat_g ?? 'N/A'))

            ->line('The user will receive a the plan now via email.');
    }


}

