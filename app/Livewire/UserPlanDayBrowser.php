<?php

namespace App\Livewire;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UserPlanDayBrowser extends Component
{
    public User $user;

    public ?int $planId = null;

    public int $currentDay = 1;

    public function mount(User $user): void
    {
        $this->user = $user;

        $activePlan = $user->plans()
            ->where('status', 'active')
            ->latest('created_at')
            ->first()
            ?? $user->plans()->latest('created_at')->first();

        $this->planId = $activePlan?->id;

        if ($activePlan && $activePlan->start_date) {
            $today = $activePlan->start_date->diffInDays(now(), false) + 1;
            $this->currentDay = max(1, min((int) $today, $activePlan->duration_days));
        }
    }

    #[Computed]
    public function plan(): ?Plan
    {
        if (! $this->planId) {
            return null;
        }

        return Plan::with([
            'mealPlans' => fn ($q) => $q->where('day_number', $this->currentDay)->with(['meals.recipe']),
            'workoutPlans' => fn ($q) => $q->where('day_number', $this->currentDay)->with(['exercises.exercise']),
        ])->find($this->planId);
    }

    public function previousDay(): void
    {
        if ($this->currentDay > 1) {
            $this->currentDay--;
        }
    }

    public function nextDay(): void
    {
        $max = $this->plan?->duration_days ?? 1;
        if ($this->currentDay < $max) {
            $this->currentDay++;
        }
    }

    public function goToDay(int $day): void
    {
        $max = $this->plan?->duration_days ?? 1;
        $this->currentDay = max(1, min($day, $max));
    }

    public function switchPlan(int $planId): void
    {
        $this->planId = $planId;
        $this->currentDay = 1;
    }

    public function render(): View
    {
        return view('livewire.user-plan-day-browser');
    }
}
