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
            ->subject('Verify Your Email - Start Your Fitness Journey!')
            ->greeting('Welcome to FitnessAI, ' . $notifiable->name . '! 🎉')
            ->line('Thank you for completing your onboarding!')
            ->line('We\'re excited to create your personalized 28-day fitness and meal plan.')
            ->line('To get started, please verify your email address by clicking the button below:')
            ->action('Verify Email & Generate My Plan', $verificationUrl)
            ->line('Once verified, we\'ll immediately start generating:')
            ->line('✅ Personalized meal plans (4 meals per day)')
            ->line('✅ Custom workout plans tailored to your goals')
            ->line('✅ 28 days of complete nutrition and exercise guidance')
            ->line('This usually takes just a few minutes.')
            ->line('If you did not create an account, no further action is required.');
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
