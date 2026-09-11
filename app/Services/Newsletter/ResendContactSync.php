<?php

namespace App\Services\Newsletter;

use App\Contracts\NewsletterContactSync;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Log;
use Resend\Client;

class ResendContactSync implements NewsletterContactSync
{
    public function __construct(private readonly Client $resend) {}

    public function sync(NewsletterSubscriber $subscriber): void
    {
        if (! config('services.resend.key') || ! app()->isProduction()) {
            Log::info('[Newsletter][Resend] Skipped sync', [
                'email' => $subscriber->email,
                'has_key' => (bool) config('services.resend.key'),
                'production' => app()->isProduction(),
            ]);

            return;
        }

        if ($subscriber->resend_contact_id) {
            return;
        }

        $segmentId = config("services.resend.segments.{$subscriber->source}")
            ?? config('services.resend.segments.default');

        try {
            $contact = $this->resend->contacts->create(array_filter([
                'email' => $subscriber->email,
                'first_name' => $subscriber->name,
                'unsubscribed' => false,
                'segments' => $segmentId ? [['id' => $segmentId]] : null,
            ], fn ($value) => $value !== null));

            $subscriber->update(['resend_contact_id' => $contact->id]);

            Log::info('[Newsletter][Resend] Contact synced', [
                'email' => $subscriber->email,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[Newsletter][Resend] Sync failed', [
                'email' => $subscriber->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
