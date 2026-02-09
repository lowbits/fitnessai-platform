<?php

namespace App\Http\Controllers\Api\V2;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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
        Log::debug("User {$id} clicked email verification link for plan generation...");
        $user = User::findOrFail($id);

        // Verify the hash matches
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return Inertia::render('EmailVerification/Invalid', [
                'message' => 'Invalid verification link',
            ]);
        }

        // Check if signature is valid
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

        $status = $this->getStatus($plan);
        $setPasswordUrl = null;
        if (!filled($plan->user->password)) {
            Log::debug("User {$id} does not have a password set. Generating password reset token...");
            $passwordResetToken = Password::broker(config('fortify.passwords'))
                ->createToken($plan->user);
            $setPasswordUrl = URL::temporarySignedRoute(
                'set-password',
                now()->addHours(24),
                [
                    'token' => $passwordResetToken,
                    'email' => $user->email,
                    'utm_source' => 'email',
                    'utm_medium' => 'app_promo',
                    'utm_campaign' => 'plan_generating',
                ]
            );
        }


        // Render plan generation page with polling
        return Inertia::render('EmailVerification/GeneratingPlan', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->plan_name,
                'start_date' => $plan->start_date->format('Y-m-d'),
                'workouts_per_week' => $plan->workouts_per_week,
            ],
            'status' => $status,
            'iosAppStoreUrl' => config('app.app_store.ios.url'),
            'setPasswordUrl' => $setPasswordUrl,
        ]);
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
            'is_complete' => $allGenerated
        ];
    }
}

