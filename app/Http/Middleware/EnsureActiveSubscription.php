<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates premium-only endpoints (e.g. the Mona coach) behind an active
 * entitlement. Free trial and paid both count. Free/expired users get a 402
 * with a `subscription_required` code the mobile client maps to opening the
 * paywall — an upsell, not a dead-end error (403s stay reserved for ownership).
 */
class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasActiveSubscription()) {
            return response()->json([
                'error' => 'subscription_required',
                'message' => 'Chatting with Mona is a premium feature — upgrade to unlock it.',
            ], Response::HTTP_PAYMENT_REQUIRED);
        }

        return $next($request);
    }
}
