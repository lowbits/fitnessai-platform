<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use App\Models\WorkoutPlan;

class WorkoutPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User|Admin $user): bool
    {
        return $user instanceof Admin || false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Admin $user, WorkoutPlan $workoutPlan): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $user->id === $workoutPlan->plan->user_id;
    }

    public function skip(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $this->update($user, $workoutPlan);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkoutPlan $workoutPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkoutPlan $workoutPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkoutPlan $workoutPlan): bool
    {
        return false;
    }
}
