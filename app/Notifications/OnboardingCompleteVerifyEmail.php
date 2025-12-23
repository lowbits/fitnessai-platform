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
        // Set locale for email based on user's preference
        app()->setLocale($notifiable->locale ?? 'en');

        $verificationUrl = $this->verificationUrl($notifiable);
        $days = config('plans.duration_days');

        return (new MailMessage)
            ->subject(__('emails.verify_email.subject'))
            ->greeting(__('emails.verify_email.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.verify_email.thanks', ['days' => $days]))
            ->line(__('emails.verify_email.verify_text'))
            ->action(__('emails.verify_email.verify_button'), $verificationUrl)
            ->line(__('emails.verify_email.what_next'))
            ->line('• ' . __('emails.verify_email.steps.create'))
            ->line('• ' . __('emails.verify_email.steps.receive'))
            ->line('• ' . __('emails.verify_email.steps.ready'))
            ->line(__('emails.verify_email.valid'))
            ->line('')
            ->line(__('emails.verify_email.ignore'))
            ->salutation(__('emails.verify_email.signature'));
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
                'locale' => $notifiable->locale ?? 'en',
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
