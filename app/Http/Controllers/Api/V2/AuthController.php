<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login with email and password (for React Native app).
     */
    public function login(Request $request): JsonResponse
    {
        Log::debug("User trying to login...");
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->where('name', $validated['device_name'])->delete();

        // Create a new API token
        $token = $user->createToken($validated['device_name'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'email_verified' => $user->email_verified_at,
            ],
            'api_token' => $token,
        ]);
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // Revoke all of the user's tokens
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully.',
        ]);
    }

    /**
     * Get the authenticated user's details.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(
            'profile'
        );

        $subscription = $user->getSubscriptionDetails();
        $currentPlan = $user->plans()->where('status', 'active')->first();


        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'profile' => [
                    'birthdate' => $user->profile?->birthdate?->format('Y-m-d'),
                    'age' => $user->profile?->age,
                    'height' => $user->profile?->height_cm,
                    'weight' => $user->getCurrentWeight(),
                    'body_goal' => $user->profile?->body_goal?->label(),
                    'diet_type' => $user->profile?->diet_type?->label(),
                    'training_place' => $user->profile?->training_place?->label(),
                    'skill_level' => $user->profile?->skill_level?->label(),
                ],
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at->toIso8601String(),
            ],
            'current_plan' => $currentPlan ? [
                'id' => $currentPlan->id,
                'name' => $currentPlan->plan_name,
                'created_at' => $currentPlan->created_at->toIso8601String(),
                'start_date' => $currentPlan->start_date->format('Y-m-d'),
                'end_date' => $currentPlan->end_date->format('Y-m-d'),
                'current_day' => $currentPlan->current_day,
                'total_days' => $currentPlan->duration_days,
                'goal' => $profile?->body_goal?->value ?? 'maintenance',
                'diet_type' => $profile?->diet_type?->value ?? 'balanced',
                'fitness_level' => $profile?->skill_level?->value ?? 'beginner',
                'nutrition_targets' => [
                    'daily_calories' => $currentPlan->daily_calories,
                    'protein_g' => $currentPlan->daily_protein_g,
                    'carbs_g' => $currentPlan->daily_carbs_g,
                    'fat_g' => $currentPlan->daily_fat_g,
                ],
            ] : null,
            'subscription' => $subscription,
            'settings' => [
                'notifications_enabled' => true,
                'workout_reminders' => true,
                'meal_reminders' => true,
                'reminder_time' => '08:00',
                'metric_system' => 'metric',
                'language' => $user->locale ?? 'en', // Use user's saved locale
                'theme' => 'dark',
            ],
        ]);
    }

    /**
     * Refresh the API token.
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_name' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        // Create a new token
        $token = $user->createToken($validated['device_name'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully.',
            'api_token' => $token,
        ]);
    }
}

