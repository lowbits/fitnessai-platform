<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V3\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['profile', 'plan']);
        $plan = $user->plan;

        return response()->json([
            'user' => new UserResource($user),
            'current_plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->plan_name,
                'start_date' => $plan->start_date->format('Y-m-d'),
                'end_date' => $plan->end_date->format('Y-m-d'),
                'current_day' => $plan->current_day,
                'total_days' => $plan->duration_days,
                'nutrition_targets' => [
                    'daily_calories' => $plan->daily_calories,
                    'protein_g' => $plan->daily_protein_g,
                    'carbs_g' => $plan->daily_carbs_g,
                    'fat_g' => $plan->daily_fat_g,
                ],
            ] : null,
            'subscription' => $user->getSubscriptionDetails(),
            'settings' => [
                'notifications_enabled' => true,
                'workout_reminders' => true,
                'meal_reminders' => true,
                'reminder_time' => '08:00',
                'metric_system' => 'metric',
                'theme' => 'dark',
            ],
        ]);
    }
}
