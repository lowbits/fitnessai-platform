<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;
use App\Support\RequestMeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterController extends Controller
{
    public function __construct(private readonly NewsletterService $newsletter) {}

    public function subscribe(NewsletterSubscribeRequest $request): JsonResponse
    {
        $locale = $request->validated('locale') ?? app()->getLocale();

        $this->newsletter->capture([
            'email' => $request->validated('email'),
            'name' => $request->validated('name'),
            'locale' => $locale,
            'country' => RequestMeta::country($request),
            'platform' => RequestMeta::platform($request->userAgent()),
            'source' => $request->validated('source') ?? 'waitlist',
            'consent_text' => trans('newsletter.consent', [], $locale),
            'consent_ip' => $request->ip(),
            'utm_source' => $request->validated('utm_source'),
            'utm_medium' => $request->validated('utm_medium'),
            'utm_campaign' => $request->validated('utm_campaign'),
            'utm_content' => $request->validated('utm_content'),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('newsletter.subscribed', [], $locale),
        ], 202);
    }

    public function confirm(Request $request, NewsletterSubscriber $subscriber): Response
    {
        $locale = $request->query('locale', $subscriber->locale ?? 'en');
        app()->setLocale(in_array($locale, ['en', 'de'], true) ? $locale : 'en');

        if (! $request->hasValidSignature()) {
            Log::info('[Newsletter][Confirm] Invalid or expired signature', [
                'subscriber' => $subscriber->getKey(),
            ]);

            return Inertia::render('Newsletter/Confirmed', ['status' => 'expired']);
        }

        $this->newsletter->confirm($subscriber);

        return Inertia::render('Newsletter/Confirmed', ['status' => 'confirmed']);
    }
}
