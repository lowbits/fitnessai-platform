<?php

namespace App\Http\Controllers\Api\V2;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Log;

class EmailVerificationController extends Controller
{
    /**
     * Verify user email and trigger plan generation.
     */
    public function verify(Request $request, string $_locale, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return Inertia::render('EmailVerification/Invalid', [
                'message' => 'Invalid verification link',
            ]);
        }

        if (! $request->hasValidSignature()) {
            Log::debug("Invalid signature for user {$id} email verification link.");

            return Inertia::render('EmailVerification/Expired', [
                'message' => 'Verification link expired',
            ]);
        }

        $planId = $request->query('plan_id');
        $plan = Plan::findOrFail($planId);

        // Mark email as verified
        if (! $user->hasVerifiedEmail()) {
            Log::debug("User {$id} email verified successfully. Triggering plan generation...");
            $user->markEmailAsVerified();
            event(new EmailVerified($user, $plan));
        }

        $token = $user->getPasswordResetToken();
        $setPasswordUrl = URL::signedRoute('set-password', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $user->load('profile');

        // Render plan generation page with polling
        return Inertia::render('EmailVerification/GeneratingPlan', [
            'user' => Inertia::once(fn () => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]),
            'plan' => Inertia::once(fn () => [
                'id' => $plan->id,
                'name' => $plan->plan_name,
                'start_date' => $plan->start_date->format('Y-m-d'),
                'workouts_per_week' => $plan->workouts_per_week,
            ]),
            'status' => fn () => $this->getStatus($plan),
            'bodyGoal' => Inertia::once(fn () => $user->profile?->body_goal?->value),
            'smartLink' => Inertia::once(fn () => URL::temporarySignedRoute(
                'download-app',
                now()->addHours(24),
                [
                    'locale' => app()->getLocale(),
                    'user' => $user->id,
                    'utm_source' => 'web',
                    'utm_medium' => 'generating_plan',
                    'utm_campaign' => 'plan_generation',
                ]
            )),
            'setPasswordUrl' => Inertia::once(fn () => $setPasswordUrl),
            'iosAppStoreUrl' => Inertia::once(fn () => config('app.app_store.ios.url')),
        ]);
    }

    /**
     * Get plan generation status for polling.
     */
    private function getStatus(Plan $plan): array
    {
        $allGenerated = $plan->generation_completed_at !== null;

        return [
            'status' => $allGenerated ? 'completed' : 'generating',
            'is_complete' => $allGenerated,
        ];
    }
}
