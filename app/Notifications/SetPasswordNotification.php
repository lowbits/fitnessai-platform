<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Log;
use URL;

class SetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

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
        // Generate signed URL that expires in 24 hours
        $setPasswordUrl = URL::temporarySignedRoute(
            'set-password',
            now()->addHours(24),
            [
                'locale' => $notifiable->preferredLocale(),
                'token' => $this->token,
                'email' => $notifiable->email,
            ]
        );

        // Log in development
        if (app()->environment('local')) {
            $deepLink = $this->generateDeepLink($notifiable->email, $this->token);
            $simulatorCommand = 'xcrun simctl openurl booted "' . $deepLink . '"';

            Log::info('🔗 Set Password Link (Signed):', [
                'url' => $setPasswordUrl,
                'command' => $simulatorCommand,
                'email' => $notifiable->email,
                'token' => $this->token,
                'expires' => now()->addHours(24)->toDateTimeString(),
            ]);

            // Also output to console/stdout
            echo "\n========================================\n";
            echo "📱 iOS Simulator Command (click to open):\n";
            echo $simulatorCommand . "\n";
            echo "========================================\n\n";
        }

        return (new MailMessage)
            ->subject(__('emails.beta_invite.subject'))
            ->greeting(__('emails.beta_invite.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.beta_invite.intro'))
            ->line(__('emails.beta_invite.description'))
            ->line(__('emails.beta_invite.steps_title'))
            ->line(__('emails.beta_invite.step_1', ['app_store_url' => config('app.app_store.ios.testflight_url')]))
            ->line(__('emails.beta_invite.step_2'))
            ->line(__('emails.beta_invite.step_3'))
            ->action(__('emails.beta_invite.action'), $setPasswordUrl)
            ->line(__('emails.beta_invite.link_expiry'))
            ->line(__('emails.beta_invite.feedback'))
            ->line(__('emails.beta_invite.instagram'))
            ->line(__('emails.beta_invite.support'))
            ->line('')
            ->line('')
            ->line(__('emails.beta_invite.salutation'))
            ->salutation(__('emails.beta_invite.signature'));

    }

    /**
     * Generate a deep link for the mobile app only development.
     */
    private function generateDeepLink(string $email, string $token): string
    {
        // This creates a deep link that your React Native app can handle
        // Format: fitnessai://set-password?email=...&token=...
        $params = http_build_query([
            'email' => $email,
            'token' => $token,
        ]);

        return 'fytrr://set-password?' . $params;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'email' => $notifiable->email,
        ];
    }
}

