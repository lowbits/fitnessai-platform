<?php

namespace App\Services;

use App\Enums\DayAccess;
use App\Enums\DayGenerationStatus;
use App\Models\HealthDailyMetric;
use App\Models\MealPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutTracking;
use App\ValueObjects\DayActivity;
use App\ValueObjects\DayCompletion;
use App\ValueObjects\DayPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class PlanDayService
{
    private const MEAL_TYPE_ORDER = ['breakfast' => 1, 'lunch' => 2, 'snack' => 3, 'dinner' => 4];

    public function __construct(private readonly DayCompletionService $completion) {}

    public function build(User $user, Plan $plan, CarbonImmutable $date): DayPlan
    {
        $start = CarbonImmutable::parse($plan->start_date->toDateString());
        $access = $plan->accessOn($date, $user->hasActiveSubscription());

        if (! $access->hasContent()) {
            return $this->buildOutOfPlanDay($user, $plan, $date, $access);
        }

        $mealPlan = MealPlan::with('meals')
            ->where('plan_id', $plan->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        $workoutPlan = WorkoutPlan::with('exercises.exercise.translations')
            ->where('plan_id', $plan->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        $status = DayGenerationStatus::determine($mealPlan, $workoutPlan, $plan, $date->equalTo($start));

        if ($mealPlan) {
            $mealPlan->setRelation('meals', $this->sortMeals($mealPlan));
        }

        $displayWorkout = $workoutPlan?->status === 'generated' ? $workoutPlan : null;
        $displayWorkout?->setAttribute('is_completed', $this->workoutCompleted($user, $workoutPlan));

        return new DayPlan(
            plan: $plan,
            date: $date,
            totalDays: (int) $plan->duration_days,
            access: $access,
            status: $status,
            mealPlan: $mealPlan,
            workoutPlan: $displayWorkout,
            calorieTrackings: $this->calorieTrackings($user, $date),
            workoutTrackings: $this->workoutTrackings($user, $workoutPlan),
            weekStrip: $this->completion->strip($user, $plan, $date->toDateString()),
            completion: $this->completion->for($user, $date->toDateString(), $plan),
            activity: $this->activity($user, $date),
        );
    }

    private function buildOutOfPlanDay(User $user, Plan $plan, CarbonImmutable $date, DayAccess $access): DayPlan
    {
        [$mealPlan, $workoutPlan] = $access === DayAccess::Expired
            ? $this->teaserContent($plan, $date)
            : [null, null];

        return new DayPlan(
            plan: $plan,
            date: $date,
            totalDays: (int) $plan->duration_days,
            access: $access,
            status: DayGenerationStatus::NotGenerated,
            mealPlan: $mealPlan,
            workoutPlan: $workoutPlan,
            calorieTrackings: $this->calorieTrackings($user, $date),
            workoutTrackings: collect(),
            weekStrip: $this->completion->strip($user, $plan, $date->toDateString()),
            completion: new DayCompletion(false, false, false),
            activity: $this->activity($user, $date),
        );
    }

    /**
     * Content for an expired day's locked upsell teaser: a generated day replayed
     * from the plan, rotated by how far past the plan we are so consecutive expired
     * days cycle through it rather than repeating the final day.
     *
     * @return array{0: ?MealPlan, 1: ?WorkoutPlan}
     */
    private function teaserContent(Plan $plan, CarbonImmutable $date): array
    {
        $days = MealPlan::where('plan_id', $plan->id)
            ->where('status', 'generated')
            ->orderBy('date')
            ->pluck('date');

        if ($days->isEmpty()) {
            return [null, null];
        }

        $start = CarbonImmutable::parse($plan->start_date->toDateString());
        $offset = (int) $start->diffInDays($date);
        $replayDate = $days[$offset % $days->count()];

        $mealPlan = MealPlan::with('meals')
            ->where('plan_id', $plan->id)
            ->whereDate('date', $replayDate)
            ->first();
        if ($mealPlan) {
            $mealPlan->setRelation('meals', $this->sortMeals($mealPlan));
        }

        $workoutPlan = WorkoutPlan::with('exercises.exercise.translations')
            ->where('plan_id', $plan->id)
            ->where('status', 'generated')
            ->whereDate('date', $replayDate)
            ->first();

        return [$mealPlan, $workoutPlan];
    }

    private function sortMeals(MealPlan $mealPlan): Collection
    {
        return $mealPlan->meals
            ->sortBy(fn ($meal) => self::MEAL_TYPE_ORDER[strtolower($meal->type)] ?? 99)
            ->values();
    }

    private function workoutCompleted(User $user, WorkoutPlan $workoutPlan): bool
    {
        return WorkoutTracking::where('user_id', $user->id)
            ->where('workout_plan_id', $workoutPlan->id)
            ->whereNotNull('completed_at')
            ->exists();
    }

    private function activity(User $user, CarbonImmutable $date): DayActivity
    {
        $metric = HealthDailyMetric::where('user_id', $user->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        return DayActivity::build($user, $metric);
    }

    private function calorieTrackings(User $user, CarbonImmutable $date): Collection
    {
        return $user->calorieTrackings()
            ->with('meal:id,name,type')
            ->whereDate('tracked_date', $date)
            ->orderBy('created_at')
            ->get();
    }

    private function workoutTrackings(User $user, ?WorkoutPlan $workoutPlan): Collection
    {
        if (! $workoutPlan) {
            return collect();
        }

        return WorkoutTracking::with('exercises.sets')
            ->where('user_id', $user->id)
            ->where('workout_plan_id', $workoutPlan->id)
            ->orderBy('started_at')
            ->get();
    }
}
