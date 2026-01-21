<?php

namespace App\Notifications;

use App\Enums\UserSource;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class PlanGenerationComplete extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Plan $plan
    )
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        if ($notifiable->routeNotificationForExpo()->isNotEmpty()) {
            $channels[] = ExpoChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $locale = $notifiable->locale ?? 'en';
        // Set locale for email based on user's preference
        app()->setLocale($locale);

        // Load plan with all relations for PDF generation
        $this->plan->load(['mealPlans.meals', 'workoutPlans.exercises']);

        // Generate Meal Plan PDF
        $mealPlanPdf = PDF::loadView('pdf.nutrition_plan', [
            'user' => $notifiable,
            'plan' => $this->plan,
            'mealPlans' => $this->plan->mealPlans()->with('meals')->orderBy('day_number')->get(),
        ]);

        // Generate Workout Plan PDF
        $workoutPlanPdf = PDF::loadView('pdf.workout_plan', [
            'user' => $notifiable,
            'plan' => $this->plan,
            'workoutPlans' => $this->plan->workoutPlans()->with('exercises')->orderBy('day_number')->get(),
        ]);

        $days = config('plans.duration_days');
        $isMobileAppOnboarding = $notifiable->source == UserSource::MOBILE_APPLE;

        $introKey = $isMobileAppOnboarding ? 'intro_app' : 'intro_web';

        $mail = (new MailMessage)
            ->subject(__('emails.plan_ready.subject'))
            ->greeting(__('emails.plan_ready.greeting', ['name' => $notifiable->name]))
            ->line(__("emails.plan_ready.$introKey", ['days' => $days]))
            ->line(__('emails.plan_ready.tailored'))
            ->line('• ' . __('emails.plan_ready.features.meals', ['days' => $days]))
            ->line('• ' . __('emails.plan_ready.features.workouts'))
            ->line('• ' . __('emails.plan_ready.features.ingredients'))
            ->line('• ' . __('emails.plan_ready.features.exercises'))
            ->line(__('emails.plan_ready.review'));


        if ($isMobileAppOnboarding) {
            $mail->action(__('emails.plan_ready.cta_app'), route('mobile.open-dashboard', ['locale' => $locale]));
        } else {
            $mail
                ->attachData($mealPlanPdf->output(), 'Meal_Plan_' . $this->plan->id . '.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->attachData($workoutPlanPdf->output(), 'Workout_Plan_' . $this->plan->id . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }


        $mail
            ->line(__('emails.plan_ready.disclaimer_title'))
            ->line(__('emails.plan_ready.disclaimer_text'))
            ->line(__('emails.plan_ready.confidence'))
            ->line('')
            ->line(__('emails.plan_ready.signature'))
            ->salutation(__('emails.plan_ready.team'));

        return $mail;
    }


    public function toExpo(object $notifiable): ExpoMessage
    {
        return ExpoMessage::create()
            ->title(__('notifications.plan_completed.title'))
            ->body(__('notifications.plan_completed.body'))
            ->data([
                'type' => 'plan_ready',
                'screen' => 'Home',
            ])
            ->channelId('plans')
            ->badge(1)
            ->priority('high');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plan_id' => $this->plan->id,
            'plan_name' => $this->plan->plan_name,
        ];
    }
}

