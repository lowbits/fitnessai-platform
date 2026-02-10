<?php

namespace App\Http\Controllers\Api\V2;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Log;

class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService
    )
    {
    }

    /**
     * Verify user email and trigger plan generation.
     */
    public function verify(Request $request, string $_locale, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return Inertia::render('EmailVerification/Invalid', [
                'message' => 'Invalid verification link',
            ]);
        }

        if (!$request->hasValidSignature()) {
            Log::debug("Invalid signature for user {$id} email verification link.");
            return Inertia::render('EmailVerification/Expired', [
                'message' => 'Verification link expired',
            ]);
        }

        $planId = $request->query('plan_id');
        $plan = Plan::findOrFail($planId);

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            Log::debug("User {$id} email verified successfully. Triggering plan generation...");
            $user->markEmailAsVerified();
            event(new EmailVerified($user, $plan));
        }


        // Render plan generation page with polling
        return Inertia::render('EmailVerification/GeneratingPlan', [
            'user' => Inertia::once(fn() => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]),
            'plan' => Inertia::once(fn() => [
                'id' => $plan->id,
                'name' => $plan->plan_name,
                'start_date' => $plan->start_date->format('Y-m-d'),
                'workouts_per_week' => $plan->workouts_per_week,
            ]),
            'status' => fn() => $this->getStatus($plan),
            'iosAppStoreUrl' => Inertia::once(fn() => config('app.app_store.ios.url')),
            'iosAppStoreQrCode' => Inertia::once(fn() => $this->qrCodeService->generate(config('app.app_store.ios.url'))),
            'setPasswordUrl' => Inertia::once(fn() => $this->generateSetPasswordUrl($plan)),
            'setPasswordQrCode' => Inertia::once(fn() => $this->generateSetPasswordQrCode($plan)),
        ]);
    }


    private function generateSetPasswordUrl(Plan $plan): ?string
    {
        if (filled($plan->user->password)) return null;

        return URL::temporarySignedRoute('set-password', now()->addHours(24), [
            'token' => $plan->user->getPasswordResetToken(),
            'email' => $plan->user->email,
            'utm_source' => 'email',
            'utm_medium' => 'app_promo',
            'utm_campaign' => 'plan_generating',
        ]);
    }

    private function generateSetPasswordQrCode(Plan $plan): ?string
    {
        $url = $this->generateSetPasswordUrl($plan);
        if (!$url) return null;

        return $this->qrCodeService->generate($url);
    }

    /**
     * Get plan generation status for polling.
     */
    private function getStatus(Plan $plan)
    {
        $allGenerated = $plan->generation_completed_at !== null;

        $status = 'generating';

        if ($allGenerated) {
            $status = 'completed';
        }

        return [
            'status' => $status,
            'is_complete' => $allGenerated,
        ];
    }
}
