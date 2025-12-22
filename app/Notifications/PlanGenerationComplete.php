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
        $dashboardUrl = config('app.url') . '/dashboard';

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

        return (new MailMessage)
            ->subject('Your Personalized Fitness Plan is Ready! 🎉')
            ->greeting('Great News, ' . $notifiable->name . '! 💪')
            ->line('Your personalized ' . config('plans.duration_days', 28) . '-day fitness and meal plan has been generated successfully!')
            ->line('We\'ve created:')
            ->line('✅ ' . config('plans.duration_days') . ' days of customized meal plans (' . config('plans.meals_per_day') . ' meals per day)')
            ->line('✅ Personalized workout routines tailored to your goals')
            ->line('✅ Detailed recipes with ingredients and instructions')
            ->line('✅ Exercise guides with form cues and alternatives')
            ->line('Your complete plans are attached to this email as PDF files for your convenience.')
            ->action('View Your Plan Online', $dashboardUrl)
            ->attachData($mealPlanPdf->output(), 'Meal_Plan_' . $this->plan->id . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($workoutPlanPdf->output(), 'Workout_Plan_' . $this->plan->id . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line('Your journey to a healthier you starts now!')
            ->line('Best of luck on your fitness journey! 🚀');
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

