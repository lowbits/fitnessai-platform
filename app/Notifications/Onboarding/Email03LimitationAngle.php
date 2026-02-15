<?php

namespace App\Notifications\Onboarding;

use App\Enums\TrainingPlace;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class Email03LimitationAngle extends Notification implements ShouldQueue
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

        $trainingPlace = $notifiable->profile?->training_place ?? TrainingPlace::GYM;
        if ($trainingPlace === TrainingPlace::NO_PREFERENCE) {
            $trainingPlace = TrainingPlace::GYM;
        }

        $scenarioKey = $trainingPlace->value;
        $scenarios = __("emails.onboarding.email_03.scenarios.$scenarioKey");

        $mail = (new MailMessage)
            ->subject(__('emails.onboarding.email_03.subject', ['goal' => $goalLabel]))
            ->greeting(__('emails.onboarding.email_03.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.onboarding.email_03.intro'))
            ->line('')
            ->line(__('emails.onboarding.email_03.pain_point'))
            ->line('')
            ->line(__('emails.onboarding.email_03.pdf_limitation'))
            ->line('');

        foreach ($scenarios as $scenario) {
            $mail->line($scenario);
        }

        $mail
            ->line('')
            ->line(__('emails.onboarding.email_03.trial'))
            ->action(__('emails.onboarding.email_03.cta'), URL::temporarySignedRoute(
                'download-app',
                now()->addDays(7),
                [
                    'locale' => $locale,
                    'user' => $notifiable->id,
                    'utm_source' => 'email',
                    'utm_medium' => 'onboarding',
                    'utm_campaign' => 'onboarding_03',
                ]
            ))
            ->salutation(__('emails.onboarding.email_03.team'));

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
