<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\UserSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\MobileOnboardingRequest;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use App\Notifications\NewOnboardingStarted;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class MobileOnboardingController extends Controller
{
    public function store(MobileOnboardingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated, $request) {
            $source = $validated['source'] ?? UserSource::MOBILE_APPLE;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => isset($validated['password'])
                    ? Hash::make($validated['password'])
                    : null,
                'locale' => $validated['language'] ?? $request->header('Accept-Language', 'en'),
                'source' => $source,
                'trial_ends_at' => now()->addDays(config('subscription.trial_days')),
            ]);

            $profile = $user->profile()->create([
                // Body & training
                'age' => Carbon::parse($validated['birthdate'])->age,
                'gender' => $validated['gender'],
                'weight_kg' => $validated['weight'],
                'height_cm' => $validated['height'],
                'body_goal' => $validated['body_goal'],
                'skill_level' => $validated['skill_level'],
                'activity_level' => $validated['activity_level'],
                'training_place' => $validated['training_place'],
                'training_sessions_per_week' => $validated['training_sessions'],
                'training_days' => $validated['training_days'] ?? null,

                // Diet
                'dietary_preference' => $validated['dietary_preference'],
                'diet_style' => $validated['diet_style'] ?? null,

                // Meal preferences (API → DB field mapping)
                'selected_meals' => $validated['selected_meals'] ?? null,
                'food_dislikes' => $validated['dislikes'] ?? [],
                'cooking_preference' => $validated['cooking_time'] ?? 'normal',
                'meal_variety' => $validated['meal_variety'] ?? 'medium',
                'meal_prep_enabled' => $validated['meal_prep_enabled'] ?? false,
                'favorite_meals' => $validated['favorite_meals'] ?? null,

                // Limitations
                'physical_limitations' => $validated['limitations'] ?? [],
                'physical_limitations_note' => $validated['limitations_note'] ?? null,
            ]);

            $dailyCalories = $profile->calculateDailyCalories();
            $macros = $profile->calculateMacros();

            $trialDays = (int) config('subscription.trial_days');

            $plan = $user->plans()->create([
                'plan_name' => $profile->body_goal->label().' Plan',
                'start_date' => now(),
                'duration_days' => $trialDays,
                'end_date' => now()->addDays($trialDays),
                'daily_calories' => $dailyCalories,
                'daily_protein_g' => $macros->proteinGrams,
                'daily_carbs_g' => $macros->carbsGrams,
                'daily_fat_g' => $macros->fatGrams,
                'workouts_per_week' => $validated['training_sessions'],
            ]);

            // Generate day 1 fast, then remaining trial days
            GenerateUserWorkoutPlan::dispatch($user, $plan, maxDays: 1);
            GenerateUserMealPlan::dispatch($user, $plan, maxDays: 1);

            GenerateUserWorkoutPlan::dispatch($user, $plan);
            GenerateUserMealPlan::dispatch($user, $plan);

            return [
                'user' => $user,
                'profile' => $profile,
                'plan' => $plan,
                'has_password' => isset($validated['password']),
            ];
        });

        $result['user']->notify(new OnboardingCompleteVerifyEmail($result['plan']));

        $this->notifyAdmins($result['user'], $validated);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed successfully.',
            'user' => [
                'id' => $result['user']->id,
                'email' => $result['user']->email,
                'name' => $result['user']->name,
                'email_verified' => false,
            ],
            'profile' => $result['profile'],
        ], 201);
    }

    private function notifyAdmins(User $user, array $profileData): void
    {
        $adminEmails = config('app.admin_emails');

        Notification::route('mail', $adminEmails)
            ->notify((new NewOnboardingStarted($user, $profileData))->delay(now()->addSeconds(5)));
    }
}
