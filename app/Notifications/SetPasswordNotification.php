<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $locale = $notifiable->locale ?? 'en';
        app()->setLocale($locale);

        $deepLink = $this->generateDeepLink($notifiable->email, $this->token);

        // Log simulator command in development environment
        if (app()->environment('local')) {
            $simulatorCommand = 'xcrun simctl openurl booted "' . $deepLink . '"';
            \Log::info('📱 iOS Simulator Deep Link Command:', [
                'command' => $simulatorCommand,
                'email' => $notifiable->email,
                'token' => $this->token,
            ]);

            // Also output to console/stdout
            echo "\n========================================\n";
            echo "📱 iOS Simulator Command (click to open):\n";
            echo $simulatorCommand . "\n";
            echo "========================================\n\n";
        }

        return (new MailMessage)
            ->subject(__('Set Your Password'))
            ->greeting(__('Hello :name!', ['name' => $notifiable->name]))
            ->line(__('You recently created an account with us. To secure your account, please set a password.'))
            ->line(__('Click the button below to set your password in our mobile app:'))
            ->action(__('Set Password'), $deepLink)
            ->line(__('Or use this code in the app: :token', ['token' => $this->token]))
            ->line(__('This link will expire in 24 hours.'))
            ->line(__('If you did not create an account, no further action is required.'));
    }

    /**
     * Generate a deep link for the mobile app.
     */
    private function generateDeepLink(string $email, string $token): string
    {
        // This creates a deep link that your React Native app can handle
        // Format: fitnessai://set-password?email=...&token=...
        $params = http_build_query([
            'email' => $email,
            'token' => $token,
        ]);

        return 'fitnessai://set-password?' . $params;
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

