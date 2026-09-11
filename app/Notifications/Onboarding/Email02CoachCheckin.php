<?php

namespace App\Notifications\Onboarding;

use App\Mail\AppMailMessage;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

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
        $workoutName = $firstWorkout?->workout_name
            ?: __('emails.onboarding.email_02.workout_fallback');

        $bannerLocale = $locale === 'de' ? 'de' : 'en';
        $bannerUrl = asset("assets/images/app/fytrr-app-home-{$bannerLocale}-top.png");

        $appUrl = URL::temporarySignedRoute('download-app', now()->addDays(7), [
            'locale' => $locale,
            'user' => $notifiable->id,
            'utm_source' => 'email',
            'utm_medium' => 'onboarding',
            'utm_campaign' => 'onboarding_02',
        ]);

        $mail = (new AppMailMessage)
            ->subject(__('emails.onboarding.email_02.subject'))
            ->greeting(__('emails.onboarding.email_02.greeting', ['name' => $notifiable->name]))
            ->previewText(__('emails.onboarding.email_02.preview'))
            ->campaign('onboarding_02')
            ->line(__('emails.onboarding.email_02.intro', ['workout' => $workoutName]))
            ->line(__('emails.onboarding.email_02.tip'))
            ->line('')
            ->line(__('emails.onboarding.email_02.pdf_gap'))
            ->line('')
            ->line(new HtmlString($this->appCard($bannerUrl, $appUrl)))
            ->line('')
            ->line(__('emails.onboarding.email_02.app_reassure'))
            ->action(__('emails.onboarding.email_02.cta', ['workout' => $workoutName]), $appUrl)
            ->line(__('emails.onboarding.email_02.closing'));

        $blogLinks = $this->blogLinks($locale);

        if ($blogLinks->isNotEmpty()) {
            $mail->line(new HtmlString($this->blogSection($blogLinks)));
        }

        return $mail->salutation(new HtmlString($this->signature()));
    }

    private function appCard(string $bannerUrl, string $appUrl): string
    {
        $bridge = e(__('emails.onboarding.email_02.app_bridge'));
        $alt = e(__('emails.onboarding.email_02.banner_alt'));

        $bullets = collect([
            __('emails.onboarding.email_02.feature_swap'),
            __('emails.onboarding.email_02.feature_checkin'),
            __('emails.onboarding.email_02.feature_tracking'),
        ])->map(fn (string $b): string => '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 11px 0;"><tr>'
            .'<td width="20" valign="top" style="font-family:sans-serif;font-size:14px;line-height:1.4;color:#16a34a;font-weight:bold;">✓</td>'
            .'<td valign="top" style="font-family:sans-serif;font-size:14px;line-height:1.4;color:#2c3a33;">'.e($b).'</td>'
            .'</tr></table>')->implode('');

        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:6px 0;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:520px;background:#f4faf6;border:1px solid #dce9e1;border-radius:18px;">'
            .'<tr>'
            .'<td class="app-text" width="60%" valign="top" style="padding:26px 8px 26px 28px;">'
            .'<div style="font-family:sans-serif;font-weight:bold;font-size:14px;line-height:1.35;color:#0c1310;margin:0 0 16px 0;">'.$bridge.'</div>'
            .$bullets
            .'</td>'
            .'<td class="app-phone" width="40%" valign="bottom" align="center" style="padding:24px 0 0 0;">'
            .'<a href="'.$appUrl.'" style="text-decoration:none;">'
            .'<img src="'.$bannerUrl.'" width="140" style="width:140px;max-width:100%;height:auto;display:block;margin:0 auto;border:0;" alt="'.$alt.'">'
            .'</a>'
            .'</td>'
            .'</tr></table></td></tr></table>';
    }

    private function signature(): string
    {
        $avatar = asset('assets/images/mona-avatar.png');
        $sign = asset('assets/images/mona-signature.png');
        $name = e(__('emails.onboarding.email_02.signature_name'));
        $role = e(__('emails.onboarding.email_02.signature_role'));

        return '<div style="margin-top:50px;">'
            .'<table role="presentation" cellpadding="0" cellspacing="0"><tr>'
            .'<td valign="middle" style="padding-right:21px;">'
            .'<img src="'.$avatar.'" width="64" height="64" style="width:64px;height:64px;border-radius:50%;display:block;border:0;" alt="">'
            .'</td>'
            .'<td valign="middle" style="font-family:sans-serif;">'
            .'<img src="'.$sign.'" width="104" style="width:104px;height:auto;display:block;border:0;margin:0 0 4px 0;" alt="'.$name.'">'
            .'<div style="font-size:12px;color:#5c6b62;line-height:1.25;">'.$role.'</div>'
            .'</td>'
            .'</tr></table></div>';
    }

    private function blogSection(Collection $links): string
    {
        $read = e(__('emails.onboarding.email_02.blog_read'));
        $titleStyle = 'font-family:sans-serif;font-size:14px;font-weight:bold;line-height:1.3;color:#0c1310;text-decoration:none;';
        $readStyle = 'font-family:sans-serif;font-size:12px;line-height:1.3;color:#16a34a;text-decoration:none;';

        $rows = $links->map(function (array $post) use ($read, $titleStyle, $readStyle): string {
            $thumb = $post['thumb']
                ? '<td width="72" valign="top" style="padding-right:14px;">'
                    .'<a href="'.$post['url'].'"><img src="'.$post['thumb'].'" width="60" height="60" style="width:60px;height:60px;border-radius:12px;display:block;border:0;" alt=""></a>'
                    .'</td>'
                : '';

            return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px 0;"><tr>'
                .$thumb
                .'<td valign="middle">'
                .'<a href="'.$post['url'].'" style="'.$titleStyle.'">'.e($post['title']).'</a>'
                .'<div style="margin-top:4px;"><a href="'.$post['url'].'" style="'.$readStyle.'">'.$read.'</a></div>'
                .'</td>'
                .'</tr></table>';
        })->implode('');

        return '<div style="border-top:1px solid #e6ebe8;margin-top:38px;padding-top:28px;">'
            .'<div style="font-family:sans-serif;font-size:13px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;color:#5c6b62;margin:0 0 20px 0;">'
            .e(__('emails.onboarding.email_02.blog_heading')).'</div>'
            .$rows
            .'</div>';
    }

    /**
     * @return Collection<int, array{title: string, url: string, thumb: ?string}>
     */
    private function blogLinks(string $locale): Collection
    {
        return collect(config("blog.{$locale}", []))
            ->take(3)
            ->map(fn (array $article, string $slug): array => [
                'title' => $article['h1'],
                'url' => url("/{$locale}/blog/{$slug}").'?utm_source=email&utm_medium=onboarding&utm_campaign=onboarding_02',
                'thumb' => $this->blogThumb($article['og_image'] ?? null),
            ])
            ->values();
    }

    private function blogThumb(?string $ogImage): ?string
    {
        if (! $ogImage) {
            return null;
        }

        $base = pathinfo($ogImage, PATHINFO_FILENAME);
        $path = "assets/images/og/thumbs/{$base}.jpg";

        return file_exists(public_path($path)) ? asset($path) : null;
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
