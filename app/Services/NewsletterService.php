<?php

namespace App\Services;

use App\Enums\NewsletterStatus;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewsletterConfirmation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    /**
     * Capture a newsletter opt-in and start the double opt-in flow.
     *
     * Idempotent: an already confirmed subscriber is left untouched; a pending
     * one gets a fresh confirmation email so the link never dead-ends.
     *
     * @param  array{email: string, name?: ?string, locale?: ?string, country?: ?string, platform?: ?string, source?: ?string, consent_text?: ?string, consent_ip?: ?string, utm_source?: ?string, utm_medium?: ?string, utm_campaign?: ?string, utm_content?: ?string}  $data
     * @param  bool  $sendConfirmation  When false, the caller confirms opt-in through another proven channel (e.g. the plan email-verification click) instead of a separate confirmation email.
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

    /**
     * Confirm a subscriber (double opt-in) and sync to the Resend audience.
     */
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

        $this->syncToResendAudience($subscriber);
    }

    /**
     * Push a confirmed subscriber into the Resend audience.
     *
     * No-ops (with a log line) when the Resend key or audience id is not
     * configured, so the opt-in flow works before Resend is wired up.
     */
    public function syncToResendAudience(NewsletterSubscriber $subscriber): void
    {
        $key = config('services.resend.key');
        $audienceId = config("services.resend.audiences.{$subscriber->source}")
            ?: config('services.resend.audience_id');

        if (! $key || ! $audienceId) {
            Log::info('[Newsletter][Resend] Skipped sync, Resend not configured', [
                'email' => $subscriber->email,
            ]);

            return;
        }

        try {
            $response = Http::withToken($key)
                ->connectTimeout(3)
                ->timeout(8)
                ->retry(2, 200, throw: false)
                ->asJson()
                ->post("https://api.resend.com/audiences/{$audienceId}/contacts", [
                    'email' => $subscriber->email,
                    'first_name' => $subscriber->name,
                    'unsubscribed' => false,
                ]);

            if ($response->successful()) {
                $subscriber->update([
                    'resend_contact_id' => $response->json('id'),
                ]);
                Log::info('[Newsletter][Resend] Contact synced', [
                    'email' => $subscriber->email,
                ]);
            } else {
                Log::warning('[Newsletter][Resend] Sync failed', [
                    'email' => $subscriber->email,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[Newsletter][Resend] Sync threw', [
                'email' => $subscriber->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
