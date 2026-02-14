<?php

namespace App\Notifications;

use App\Enums\UserSource;
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

    protected ?string $passwordResetToken;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Plan $plan,
        ?string $passwordResetToken = null
    ) {
        $this->passwordResetToken = $passwordResetToken;
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
        app()->setLocale($locale);

        $this->plan->load(['mealPlans.meals', 'workoutPlans.exercises']);

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

        $days = config('plans.duration_days');
        $isMobileAppOnboarding = $notifiable->source == UserSource::MOBILE_APPLE;
        $introKey = $isMobileAppOnboarding ? 'intro_app' : 'intro_pdf';

        $badgeLocale = $locale === 'de' ? 'de-de' : 'en-us';

        $mail = (new MailMessage)
            ->subject(__('emails.plan_ready.subject'))
            ->greeting(__('emails.plan_ready.greeting', ['name' => $notifiable->name]))
            ->line(__("emails.plan_ready.$introKey", ['days' => $days]));

        if ($isMobileAppOnboarding) {
            $mail->action(__('emails.plan_ready.cta_app'), route('home', ['locale' => $locale]));
        } else {
            $mail
                ->attachData($mealPlanPdf->output(), 'Meal_Plan_'.$this->plan->id.'.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->attachData($workoutPlanPdf->output(), 'Workout_Plan_'.$this->plan->id.'.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }

        // App Promo only for web users
        if ($this->passwordResetToken && ! filled($notifiable->password)) {

            $badgeLocale = strtoupper($locale);

            // App Promo
            $mail
                ->line('')
                ->line(__('emails.plan_ready.app_pitch'))
                ->line(new HtmlString(
                    '<img src="'.asset("assets/app-promo_{$locale}.png").'" width="600" style="display:block;width:100%;max-width:600px;height:auto;border-radius:8px;margin:16px 0;" alt="fytrr App">'
                ))
                ->line(new HtmlString('<table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
            <tr>
                <td align="center">
                    <a href="'.config('app.app_store.ios.url').'">
                        <img src="'.asset("/assets/badges/App_Store_Badge_{$badgeLocale}.png").'"
                             alt="Download on App Store"
                             style="height:40px;">
                    </a>
                </td>
            </tr>
        </table>'));

            // Step 2: Password reset if needed

            $setPasswordUrl = URL::temporarySignedRoute(
                'set-password',
                now()->addHours(24),
                [
                    'token' => $this->passwordResetToken,
                    'email' => $notifiable->email,
                    'utm_source' => 'email',
                    'utm_medium' => 'app_promo',
                    'utm_campaign' => 'plan_ready',
                ]
            );

            $mail->action(__('emails.plan_ready.cta'), $setPasswordUrl);
        }

        $mail
            ->line(__('emails.plan_ready.trial'))
            ->line('')
            ->line(__('emails.plan_ready.support'))
            ->line('')
            ->line(__('emails.plan_ready.closing'))
            ->salutation(__('emails.plan_ready.team'))
            ->line('')
            ->line(new HtmlString('<p style="font-size:12px;color:#666;margin-top:24px;">'.__('emails.plan_ready.disclaimer').'</p>'));

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
