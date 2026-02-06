<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class AppPromoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Plan $plan,
        string      $token = null
    )
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $setPasswordUrl = URL::temporarySignedRoute(
            'set-password',
            now()->addHours(24),
            [
                'locale' => $locale = $notifiable->preferredLocale(),
                'token' => $this->token,
                'email' => $notifiable->email,
                'utm_source' => 'email',
                'utm_medium' => 'app_promo',
                'utm_campaign' => 'plan_ready_24h',
            ]
        );

        $promoImageUrl = asset("assets/app-promo_{$locale}.png");
        $badgeLocale = strtoupper($locale);

        return (new MailMessage)
            ->subject(__('emails.app_promo.subject'))
            ->greeting(__('emails.app_promo.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.app_promo.intro'))
            ->line(__('emails.app_promo.pitch'))
            ->line(new HtmlString(
                '<img src="' . $promoImageUrl . '" width="600" style="width:100%;max-width:600px;height:auto;border-radius:8px;" alt="fytrr App">'
            ))
            ->line('')
            ->line('✅ ' . __('emails.app_promo.features.track'))
            ->line('✅ ' . __('emails.app_promo.features.meals'))
            ->line('✅ ' . __('emails.app_promo.features.unlimited'))
            ->line('✅ ' . __('emails.app_promo.features.adaptive'))
            ->line(__('emails.app_promo.trial'))
            ->line('')
            ->line(__('emails.app_promo.step1_label'))
            ->line(new HtmlString('<table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <a href="' . config('app.app_store.ios.url') . '">
                            <img src="'. asset("/assets/badges/App_Store_Badge_{$badgeLocale}.png") .'"
                                 alt="Download on App Store"
                                 style="height:40px;">
                        </a>
                    </td>
                </tr>
            </table>'))
            ->line('')
            ->line(__('emails.app_promo.step2_label'))
            ->action(__('emails.app_promo.cta'), $setPasswordUrl)
            ->line('')
            ->line(__('emails.app_promo.signature'))
            ->salutation(__('emails.app_promo.team'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
