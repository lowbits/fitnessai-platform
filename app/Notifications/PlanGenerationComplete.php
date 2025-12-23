<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PlanGenerationComplete extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject(__('emails.plan_ready.subject'))
            ->greeting(__('emails.plan_ready.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.plan_ready.thank_you', ['days' => $days]))
            ->line(__('emails.plan_ready.tailored'))
            ->line('• ' . __('emails.plan_ready.features.meals', ['days' => $days]))
            ->line('• ' . __('emails.plan_ready.features.workouts'))
            ->line('• ' . __('emails.plan_ready.features.ingredients'))
            ->line('• ' . __('emails.plan_ready.features.exercises'))
            ->line(__('emails.plan_ready.review'))
            ->attachData($mealPlanPdf->output(), 'Meal_Plan_' . $this->plan->id . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($workoutPlanPdf->output(), 'Workout_Plan_' . $this->plan->id . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line(__('emails.plan_ready.disclaimer_title'))
            ->line(__('emails.plan_ready.disclaimer_text'))
            ->line(__('emails.plan_ready.confidence'))
            ->line('')
            ->line(__('emails.plan_ready.signature'))
            ->salutation(__('emails.plan_ready.team'));
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

