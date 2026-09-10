<?php

namespace App\Services;

use App\Enums\NewsletterStatus;
use App\Jobs\SyncNewsletterContact;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewsletterConfirmation;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    /**
     * @param  array{email: string, name?: ?string, locale?: ?string, country?: ?string, platform?: ?string, source?: ?string, consent_text?: ?string, consent_ip?: ?string, utm_source?: ?string, utm_medium?: ?string, utm_campaign?: ?string, utm_content?: ?string}  $data
     */
    public function capture(array $data, bool $sendConfirmation = true): NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::firstOrNew([
            'email' => mb_strtolower(trim($data['email'])),
        ]);

        if ($subscriber->isConfirmed()) {
            return $subscriber;
        }

        $subscriber->fill([
            'name' => $data['name'] ?? $subscriber->name,
            'locale' => $data['locale'] ?? $subscriber->locale ?? 'en',
            'country' => $data['country'] ?? $subscriber->country,
            'platform' => $data['platform'] ?? $subscriber->platform,
            'source' => $data['source'] ?? $subscriber->source,
            'consent_text' => $data['consent_text'] ?? $subscriber->consent_text,
            'consented_at' => now(),
            'consent_ip' => $data['consent_ip'] ?? $subscriber->consent_ip,
            'status' => NewsletterStatus::Pending,
            'utm_source' => $data['utm_source'] ?? $subscriber->utm_source,
            'utm_medium' => $data['utm_medium'] ?? $subscriber->utm_medium,
            'utm_campaign' => $data['utm_campaign'] ?? $subscriber->utm_campaign,
            'utm_content' => $data['utm_content'] ?? $subscriber->utm_content,
        ]);
        $subscriber->save();

        if ($sendConfirmation) {
            $subscriber->notify(new NewsletterConfirmation);
        }

        Log::info('[Newsletter][OptIn] Captured', [
            'email' => $subscriber->email,
            'source' => $subscriber->source,
            'confirmation_email' => $sendConfirmation,
        ]);

        return $subscriber;
    }

    public function confirm(NewsletterSubscriber $subscriber): void
    {
        if (! $subscriber->isConfirmed()) {
            $subscriber->update([
                'status' => NewsletterStatus::Confirmed,
                'confirmed_at' => now(),
            ]);

            Log::info('[Newsletter][Confirm] Subscriber confirmed', [
                'email' => $subscriber->email,
            ]);
        }

        SyncNewsletterContact::dispatch($subscriber);
    }
}
