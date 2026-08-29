<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\Auth\ProvisionOnboardingUser;
use App\Actions\NotifyAdmins;
use App\Actions\Plan\StartPlanGeneration;
use App\Enums\UserSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\MobileOnboardingRequest;
use App\Http\Resources\Api\V3\UserResource;
use App\Models\User;
use App\Notifications\NewOnboardingStarted;
use App\Notifications\OnboardingCompleteVerifyEmail;
use App\Support\ConsentRollout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobileOnboardingController extends Controller
{
    public function __construct(
        private readonly NotifyAdmins $notifyAdmins,
        private readonly ProvisionOnboardingUser $provision,
        private readonly StartPlanGeneration $startPlanGeneration,
    ) {}

    public function store(MobileOnboardingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'locale' => $validated['locale'] ?? $request->header('Accept-Language', 'en'),
                'source' => $validated['source'] ?? UserSource::MOBILE_APPLE,
            ]);

            $plan = $this->provision->execute($user, $validated);

            return ['user' => $user, 'plan' => $plan];
        });

        // TODO(consent-rollout): pre-consent clients have no consent screen, so
        // generate at signup; consent-capable clients defer to the consent grant.
        if (! ConsentRollout::clientCollectsConsent($request->header('X-App-Version'))) {
            $this->startPlanGeneration->execute($result['user'], $result['plan']);
        }

        $result['user']->notify(new OnboardingCompleteVerifyEmail($result['plan']));

        $this->notifyAdmins->send(
            (new NewOnboardingStarted($result['user'], $validated))->delay(now()->addSeconds(5))
        );

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed successfully.',
            'user' => new UserResource($result['user']->load(['profile', 'plan'])),
        ], 201);
    }
}
