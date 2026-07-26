<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\UpdateProfileRequest;
use App\Http\Resources\Api\V3\UserResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->fill($request->userAttributes())->save();
        $user->profile()->update($request->profileAttributes());

        return response()->json([
            'user' => new UserResource($user->fresh()->load(['profile', 'plan'])),
        ]);
    }
}
