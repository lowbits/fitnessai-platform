<?php

namespace App\Notifications;

use App\Enums\UserSource;
use App\Mail\AppMailMessage;
use App\Models\Plan;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoMessage;

class PlanGenerationComplete extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Plan $plan,
        protected ?string $passwordResetToken = null
    ) {}

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
        app()->setLocale($locale);

        $this->plan->load(['mealPlans.meals', 'workoutPlans.exercises']);

        $days = config('plans.duration_days');
        $isMobileAppOnboarding = $notifiable->source == UserSource::MOBILE_APPLE;
        $introKey = $isMobileAppOnboarding ? 'intro_app' : 'intro_pdf';

        $bodyGoal = $notifiable->profile?->body_goal;
        $goalLabel = $bodyGoal?->actionLabel($locale) ?? '';

        $firstWorkout = $this->plan->workoutPlans()->orderBy('day_number')->first();
        $firstWorkoutName = $firstWorkout?->workout_name ?? '';

        $firstMealPlan = $this->plan->mealPlans()->orderBy('day_number')->first();
        $firstMeal = $firstMealPlan?->meals()->first();
        $firstMealName = $firstMeal?->name ?? '';

        $mail = (new AppMailMessage)
            ->subject(__('emails.plan_ready.subject', ['goal' => $goalLabel]))
            ->greeting(__('emails.plan_ready.greeting', ['name' => $notifiable->name]))
            ->previewText(__('emails.plan_ready.preview'));

        $mail
            ->line(__("emails.plan_ready.$introKey", ['days' => $days, 'goal' => $goalLabel]));

        if ($isMobileAppOnboarding) {
            $mail->action(__('emails.plan_ready.cta_app'), route('home', ['locale' => $locale]));
        } else {
            $mealPlanPdf = PDF::loadView('pdf.nutrition_plan', [
                'user' => $notifiable,
                'plan' => $this->plan,
                'mealPlans' => $this->plan->mealPlans()->with('meals')->orderBy('day_number')->get(),
            ]);

            $workoutPlanPdf = PDF::loadView('pdf.workout_plan', [
                'user' => $notifiable,
                'plan' => $this->plan,
                'workoutPlans' => $this->plan->workoutPlans()->with('exercises.exercise.translations')->orderBy('day_number')->get(),
            ]);

            $mail
                ->attachData($mealPlanPdf->output(), 'Meal_Plan_'.$this->plan->id.'.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->attachData($workoutPlanPdf->output(), 'Workout_Plan_'.$this->plan->id.'.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }

        $mail
            ->line('')
            ->line(__('emails.plan_ready.first_workout', ['workout' => $firstWorkoutName]))
            ->line(__('emails.plan_ready.first_meal', ['meal' => $firstMealName]))
            ->line('')
            ->line(__('emails.plan_ready.start_cta'))
            ->line('')
            ->line(__('emails.plan_ready.closing'))
            ->salutation(__('emails.plan_ready.team'));

        // Soft P.S. about the app for web users
        if (! $isMobileAppOnboarding) {
            $mail
                ->line('')
                ->line(__('emails.plan_ready.app_pitch'))
                ->action(__('emails.plan_ready.app_cta'), URL::temporarySignedRoute(
                    'download-app',
                    now()->addHours(72),
                    [
                        'locale' => $locale,
                        'user' => $notifiable->id,
                        'utm_source' => 'email',
                        'utm_medium' => 'notification',
                        'utm_campaign' => 'plan_ready',
                    ]
                ));
        }

        $mail->line(new HtmlString('<p style="font-size:12px;color:#666;margin-top:24px;">'.__('emails.plan_ready.disclaimer').'</p>'));

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
