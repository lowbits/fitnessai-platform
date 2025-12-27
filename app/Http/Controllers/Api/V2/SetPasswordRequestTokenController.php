<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Log;


class SetPasswordRequestTokenController extends Controller
{
    /**
     * Request a set password token for a user without a password.
     */
    public function store(Request $request): JsonResponse
    {

        Log::info("User trying to set password...");

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);


        $status = Password::broker(config('fortify.passwords'))->sendResetLink(
            $validated,
            function ($user, $token) {
                if (filled($user->password)) {
                    return Password::INVALID_USER;
                }

                $user->notify(new SetPasswordNotification($token));

                return Password::RESET_LINK_SENT;
            }
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password set link sent!'])
            : response()->json(['message' => __($status)], 400);
    }
}

