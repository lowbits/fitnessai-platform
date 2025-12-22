<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOnboardingStarted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user,
        public array $profileData
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
            ->subject('New User Started Onboarding')
            ->greeting('New Onboarding Started')
            ->line('A new user has completed the onboarding form.')
            ->line('**User Details:**')
            ->line('Name: ' . $this->user->name)
            ->line('Email: ' . $this->user->email)
            ->line('Age: ' . ($this->profileData['age'] ?? 'N/A'))
            ->line('Goal: ' . ($this->profileData['body_goal'] ?? 'N/A'))
            ->line('Diet Type: ' . ($this->profileData['diet_type'] ?? 'N/A'))
            ->line('The user will receive a verification email to activate their account.');
    }


}

