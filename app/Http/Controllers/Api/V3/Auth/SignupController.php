<?php

namespace App\Http\Controllers\Api\V3\Auth;

use App\Actions\Auth\IssueApiToken;
use App\Actions\Auth\ProvisionOnboardingUser;
use App\Actions\Auth\ResolveAccountFromCredential;
use App\Actions\NotifyAdmins;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\SignupRequest;
use App\Http\Resources\Api\V3\UserResource;
use App\Notifications\NewOnboardingStarted;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SignupController extends Controller
{
    public function __construct(
        private readonly ResolveAccountFromCredential $resolve,
        private readonly ProvisionOnboardingUser $provision,
        private readonly IssueApiToken $issueToken,
        private readonly NotifyAdmins $notifyAdmins,
    ) {}

    public function __invoke(SignupRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = $this->resolve->forSignup($data);

            if ($user->wasRecentlyCreated) {
                $this->provision->execute($user, $data);
            }

            return $user;
        });

        if ($user->wasRecentlyCreated) {
            if ($data['auth']['type'] === 'password') {
                $user->notify(new OnboardingCompleteVerifyEmail($user->plan));
            }

            $this->notifyAdmins->send(
                (new NewOnboardingStarted($user, $data))->delay(now()->addSeconds(5))
            );
        }

        $token = $this->issueToken->execute($user, $data['device_name'] ?? null);

        return response()->json([
            'user' => new UserResource($user->load(['profile', 'plan'])),
            'api_token' => $token,
        ], 201);
    }
}
