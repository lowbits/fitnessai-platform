<?php

namespace App\Services\Newsletter;

use App\Contracts\NewsletterContactSync;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResendContactSync implements NewsletterContactSync
{
    public function sync(NewsletterSubscriber $subscriber): void
    {
        $key = config('services.resend.key');

        if (! $key || ! app()->isProduction()) {
            Log::info('[Newsletter][Resend] Skipped sync', [
                'email' => $subscriber->email,
                'has_key' => (bool) $key,
                'production' => app()->isProduction(),
            ]);

            return;
        }

        $segmentId = config("services.resend.segments.{$subscriber->source}")
            ?? config('services.resend.segments.default');

        try {
            $response = Http::withToken($key)
                ->connectTimeout(3)
                ->timeout(8)
                ->retry(2, 200, throw: false)
                ->asJson()
                ->post('https://api.resend.com/contacts', array_filter([
                    'email' => $subscriber->email,
                    'first_name' => $subscriber->name,
                    'unsubscribed' => false,
                    'segments' => $segmentId ? [['id' => $segmentId]] : null,
                ], fn ($value) => $value !== null));

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
