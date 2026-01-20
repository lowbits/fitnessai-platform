<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendVerificationEmailController extends Controller
{
    /**
     * Resend email verification notification.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if email is already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.',
            ], 400);
        }

        // Get the user's active plan
        $plan = $user->plans()->where('status', 'active')->first();

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'No active plan found.',
            ], 400);
        }

        // Send verification notification using the same notification as onboarding
        $user->notify(new OnboardingCompleteVerifyEmail($plan));

        return response()->json([
            'success' => true,
            'message' => 'Verification email has been sent. Please check your inbox.',
        ]);
    }
}

