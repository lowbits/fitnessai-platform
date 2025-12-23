<?php

namespace App\Http\Controllers\Api\V2;

use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{
    /**
     * Verify user email and trigger plan generation.
     */
    public function verify(Request $request, string $_locale, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verify the hash matches
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return Inertia::render('EmailVerification/Invalid', [
                'message' => 'Invalid verification link',
            ]);
        }

        // Check if signature is valid
        if (!$request->hasValidSignature()) {
            return Inertia::render('EmailVerification/Expired', [
                'message' => 'Verification link expired',
            ]);
        }

        $planId = $request->query('plan_id');
        $plan = Plan::findOrFail($planId);

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new EmailVerified($user, $plan));
        }

        $status = $this->getStatus($plan);


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

