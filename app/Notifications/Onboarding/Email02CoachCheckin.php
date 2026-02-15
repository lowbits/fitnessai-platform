<?php

namespace App\Notifications\Onboarding;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class Email02CoachCheckin extends Notification implements ShouldQueue
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

        $firstWorkout = $this->plan->workoutPlans()->orderBy('day_number')->first();
        $workoutName = $firstWorkout?->workout_name ?? '';

        return (new MailMessage)
            ->subject(__('emails.onboarding.email_02.subject'))
            ->greeting(__('emails.onboarding.email_02.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.onboarding.email_02.intro', ['workout' => $workoutName]))
            ->line('')
            ->line(__('emails.onboarding.email_02.tip'))
            ->line('')
            ->line(__('emails.onboarding.email_02.mona_pitch'))
            ->line('')
            ->line(__('emails.onboarding.email_02.closing'))
            ->line('')
            ->line(__('emails.onboarding.email_02.soft_cta', [
                'label' => 'fytrr.de/app',
                'url' => URL::temporarySignedRoute(
                    'download-app',
                    now()->addDays(7),
                    [
                        'locale' => $locale,
                        'user' => $notifiable->id,
                        'utm_source' => 'email',
                        'utm_medium' => 'onboarding',
                        'utm_campaign' => 'onboarding_02',
                    ]
                ),
            ]))
            ->salutation(__('emails.onboarding.email_02.team'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
