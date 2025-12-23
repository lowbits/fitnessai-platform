<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\ActivityLevel;
use App\Enums\BodyGoal;
use App\Enums\DietType;
use App\Enums\Gender;
use App\Enums\SkillLevel;
use App\Enums\TrainingPlace;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewOnboardingStarted;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Enum;

class OnboardingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'age' => ['required', 'integer', 'min:13', 'max:120'],
            'gender' => ['required', new Enum(Gender::class)],
            'weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'height' => ['required', 'numeric', 'min:100', 'max:250'],
            'body_goal' => ['required', new Enum(BodyGoal::class)],
            'skill_level' => ['required', new Enum(SkillLevel::class)],
            'activity_level' => ['required', new Enum(ActivityLevel::class)],
            'training_place' => ['required', new Enum(TrainingPlace::class)],
            'diet_type' => ['required', new Enum(DietType::class)],
            'training_sessions' => ['required', 'integer', 'min:1', 'max:7'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => isset($validated['password'])
                    ? Hash::make($validated['password'])
                    : null,
                'locale' => app()->getLocale(),
            ]);


            $profile = $user->profile()->create([
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'weight_kg' => $validated['weight'],
                'height_cm' => $validated['height'],
                'body_goal' => $validated['body_goal'],
                'skill_level' => $validated['skill_level'],
                'activity_level' => $validated['activity_level'],
                'training_place' => $validated['training_place'],
                'diet_type' => $validated['diet_type'],
                'training_sessions_per_week' => $validated['training_sessions'],
            ]);

            // Calculate nutrition using profile helper methods
            $dailyCalories = $profile->calculateDailyCalories();
            $macros = $profile->calculateMacros();

            $totalDays = (int) config('plans.duration_days');


            // Create plan
            $plan = $user->plans()->create([
                'plan_name' => ucfirst($validated['body_goal']) . ' Plan',
                'start_date' => now(),
                'duration_days' => $totalDays,
                'end_date' => now()->addDays($totalDays),
                'daily_calories' => $dailyCalories,
                'daily_protein_g' => $macros['protein_g'],
                'daily_carbs_g' => $macros['carbs_g'],
                'daily_fat_g' => $macros['fat_g'],
                'workouts_per_week' => $validated['training_sessions'],
            ]);


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
        $this->notifyAdmins($result['user'], $validated);

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

    /**
     * Notify admin(s) about new onboarding.
     */
    private function notifyAdmins(User $user, array $profileData): void
    {
        $adminEmails = config('app.admin_emails');

        Notification::route('mail', $adminEmails)
            ->notify(new NewOnboardingStarted($user, $profileData));

    }
}
