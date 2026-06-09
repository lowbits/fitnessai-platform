<?php

namespace App\Http\Controllers\Api\V2;

use App\Actions\NotifyAdmins;
use App\Enums\UserSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\OnboardingRequest;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use App\Notifications\NewOnboardingStarted;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    public function __construct(private readonly NotifyAdmins $notifyAdmins) {}

    public function store(OnboardingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => isset($validated['password'])
                    ? Hash::make($validated['password'])
                    : null,
                'locale' => $validated['language'] ?? $request->header('Accept-Language', 'en'),
                'source' => $validated['source'] ?? UserSource::WEB,
            ]);

            // Only mobile users get a trial period
            if ($user->source !== UserSource::WEB) {
                $user->update(['trial_ends_at' => now()->addDays(config('subscription.trial_days'))]);
            }

            $profile = $user->profile()->create([
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'weight_kg' => $validated['weight'],
                'height_cm' => $validated['height'],
                'body_goal' => $validated['body_goal'],
                'skill_level' => $validated['skill_level'],
                'activity_level' => $validated['activity_level'],
                'training_place' => $validated['training_place'],
                'dietary_preference' => $validated['dietary_preference'] ?? null,
                'diet_style' => $validated['diet_style'] ?? null,
                'training_sessions_per_week' => $validated['training_sessions'],
                'training_days' => $validated['training_days'] ?? null,
            ]);

            // Calculate nutrition using profile helper methods
            $dailyCalories = $profile->calculateDailyCalories();
            $macros = $profile->calculateMacros();

            $isMobile = $user->source !== UserSource::WEB;
            $planDays = $isMobile
                ? (int) config('subscription.trial_days')
                : (int) config('subscription.pdf_plan_days');

            $plan = $user->plans()->create([
                'plan_name' => ucfirst($validated['body_goal']).' Plan',
                'start_date' => now(),
                'duration_days' => $planDays,
                'end_date' => now()->addDays($planDays),
                'daily_calories' => $dailyCalories,
                'daily_protein_g' => $macros->proteinGrams,
                'daily_carbs_g' => $macros->carbsGrams,
                'daily_fat_g' => $macros->fatGrams,
                'workouts_per_week' => $validated['training_sessions'],
            ]);

            // Mobile: generate day 1 fast, then remaining trial days.
            // Web: generation triggered after email verification via listeners.
            if ($isMobile) {
                GenerateUserWorkoutPlan::dispatch($user, $plan, maxDays: 1);
                GenerateUserMealPlan::dispatch($user, $plan, maxDays: 1);

                GenerateUserWorkoutPlan::dispatch($user, $plan);
                GenerateUserMealPlan::dispatch($user, $plan);
            }

            return [
                'user' => $user,
                'profile' => $profile,
                'plan' => $plan,
                'has_password' => isset($validated['password']),
            ];
        });

        // Send verification email
        $result['user']->notify(new OnboardingCompleteVerifyEmail($result['plan']));

        // Notify admin(s) about new onboarding
        $this->notifyAdmins->send(
            (new NewOnboardingStarted($result['user'], $validated))->delay(now()->addSeconds(5))
        );

        $response = [
            'success' => true,
            'message' => 'Onboarding completed successfully. Please check your email to verify your account and start generating your personalized plan.',
            'user' => [
                'id' => $result['user']->id,
                'email' => $result['user']->email,
                'name' => $result['user']->name,
                'email_verified' => false,
            ],
            'profile' => $result['profile'],
        ];

        return response()->json($response, 201);
    }
}
