<?php

namespace App\Http\Controllers\Api\V3;

use App\Actions\NotifyAdmins;
use App\Enums\UserSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\MobileOnboardingRequest;
use App\Http\Resources\Api\V3\UserResource;
use App\Jobs\GenerateUserMealPlan;
use App\Jobs\GenerateUserWorkoutPlan;
use App\Models\User;
use App\Notifications\NewOnboardingStarted;
use App\Notifications\OnboardingCompleteVerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MobileOnboardingController extends Controller
{
    public function __construct(private readonly NotifyAdmins $notifyAdmins) {}

    public function store(MobileOnboardingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated, $request) {
            $source = $validated['source'] ?? UserSource::MOBILE_APPLE;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'locale' => $validated['locale'] ?? $request->header('Accept-Language', 'en'),
                'source' => $source,
                'trial_ends_at' => now()->addDays(config('subscription.trial_days')),
            ]);

            $profile = $user->profile()->create([
                'birthdate' => Carbon::parse($validated['birthdate'])->startOfDay(),
                'gender' => $validated['gender'],
                'weight_kg' => $validated['weight_kg'],
                'height_cm' => $validated['height_cm'],
                'body_goal' => $validated['body_goal'],
                'skill_level' => $validated['skill_level'],
                'activity_level' => $validated['activity_level'],
                'training_place' => $validated['training_place'],
                'training_sessions_per_week' => $validated['training_sessions_per_week'],
                'training_days' => $validated['training_days'] ?? null,
                'dietary_preference' => $validated['dietary_preference'],
                'selected_meals' => $validated['selected_meals'] ?? null,
                'food_dislikes' => $validated['food_dislikes'] ?? [],
                'disliked_recipe_ids' => $validated['disliked_recipes'] ?? [],
                'cooking_preference' => $validated['cooking_preference'] ?? 'normal',
                'cooking_frequency' => $validated['cooking_frequency'] ?? null,
                'meal_variety' => $validated['meal_variety'] ?? 'medium',
                'physical_limitations' => $validated['physical_limitations'] ?? [],
                'physical_limitations_note' => $validated['physical_limitations_note'] ?? null,
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
                'workouts_per_week' => $validated['training_sessions_per_week'],
            ]);

            // Save recipe favorites
            if (! empty($validated['favorite_recipes'])) {
                $user->favoriteRecipes()->attach($validated['favorite_recipes']);
                Log::info('[Onboarding] Recipe favorites saved', [
                    'user_id' => $user->id,
                    'recipe_ids' => $validated['favorite_recipes'],
                ]);
            }

            // Generate day 1 fast, then remaining trial days
            GenerateUserWorkoutPlan::dispatch($user, $plan, maxDays: 1);
            GenerateUserMealPlan::dispatch($user, $plan, maxDays: 1);

            GenerateUserWorkoutPlan::dispatch($user, $plan);
            GenerateUserMealPlan::dispatch($user, $plan);

            return [
                'user' => $user,
                'profile' => $profile,
                'plan' => $plan,
                'has_password' => true,
            ];
        });

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
