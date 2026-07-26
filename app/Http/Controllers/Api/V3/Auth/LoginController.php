<?php

namespace App\Http\Controllers\Api\V3\Auth;

use App\Actions\Auth\IssueApiToken;
use App\Actions\Auth\ResolveAccountFromCredential;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\SocialLoginRequest;
use App\Http\Resources\Api\V3\UserResource;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __construct(
        private readonly ResolveAccountFromCredential $resolve,
        private readonly IssueApiToken $issueToken,
    ) {}

    public function __invoke(SocialLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->resolve->forLogin($data);
        $token = $this->issueToken->execute($user, $data['device_name'] ?? null);

        return response()->json([
            'user' => new UserResource($user->load(['profile', 'plan'])),
            'api_token' => $token,
        ]);
    }
}
