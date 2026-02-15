<?php

namespace App\Notifications\Onboarding;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class Email05LastChance extends Notification implements ShouldQueue
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

        $sessionsPerWeek = $notifiable->profile?->training_sessions_per_week ?? 3;

        return (new MailMessage)
            ->subject(__('emails.onboarding.email_05.subject'))
            ->greeting(__('emails.onboarding.email_05.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.onboarding.email_05.intro', ['goal' => $goalLabel, 'sessions' => $sessionsPerWeek]))
            ->line('')
            ->line(__('emails.onboarding.email_05.if_yes'))
            ->line(__('emails.onboarding.email_05.if_no'))
            ->line('')
            ->line(__('emails.onboarding.email_05.mona_pitch'))
            ->line('')
            ->line(__('emails.onboarding.email_05.trial'))
            ->action(__('emails.onboarding.email_05.cta'), URL::temporarySignedRoute(
                'download-app',
                now()->addDays(7),
                [
                    'locale' => $locale,
                    'user' => $notifiable->id,
                    'utm_source' => 'email',
                    'utm_medium' => 'onboarding',
                    'utm_campaign' => 'onboarding_05',
                ]
            ))
            ->line('')
            ->line(__('emails.onboarding.email_05.last_email'))
            ->salutation(__('emails.onboarding.email_05.team'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
