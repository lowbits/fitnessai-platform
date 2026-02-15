<?php

namespace App\Notifications\Onboarding;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class Email04SocialProof extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Plan $plan) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->preferredLocale();
        app()->setLocale($locale);

        $bodyGoal = $notifiable->profile?->body_goal;
        $goalLabel = $bodyGoal?->actionLabel($locale) ?? '';

        return (new MailMessage)
            ->subject(__('emails.onboarding.email_04.subject'))
            ->greeting(__('emails.onboarding.email_04.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.onboarding.email_04.intro', ['goal' => $goalLabel]))
            ->line('')
            ->line(__('emails.onboarding.email_04.testimonial_intro'))
            ->line('')
            ->line(__('emails.onboarding.email_04.testimonial'))
            ->line(__('emails.onboarding.email_04.testimonial_author'))
            ->line('')
            ->line(__('emails.onboarding.email_04.outro'))
            ->action(__('emails.onboarding.email_04.cta'), URL::temporarySignedRoute(
                'download-app',
                now()->addDays(7),
                [
                    'locale' => $locale,
                    'user' => $notifiable->id,
                    'utm_source' => 'email',
                    'utm_medium' => 'onboarding',
                    'utm_campaign' => 'onboarding_04',
                ]
            ))
            ->salutation(__('emails.onboarding.email_04.team'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
