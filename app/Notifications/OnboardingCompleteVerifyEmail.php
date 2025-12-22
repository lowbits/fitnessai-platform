<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OnboardingCompleteVerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Plan $plan
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('One Step Away from Your Personal Fitness Plan')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Thanks for signing up! You\'re one click away from getting your personalized ' . config('plans.duration_days') . '-day plan.')
            ->line('Just verify your email address to get started:')
            ->action('Verify Email Address', $verificationUrl)
            ->line('**What happens next:**')
            ->line('• We\'ll create your custom meal and workout plans')
            ->line('• You\'ll receive everything as PDF attachments')
            ->line('• Ready to use in about 3-5 minutes')
            ->line('The verification link is valid for 24 hours.')
            ->line('')
            ->line('Didn\'t sign up? You can safely ignore this email.')
            ->salutation('See you soon!');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify-onboarding',
            now()->addHours(24),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
                'plan_id' => $this->plan->id,
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plan_id' => $this->plan->id,
        ];
    }
}
