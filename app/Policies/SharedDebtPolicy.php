<?php

namespace App\Policies;

use App\Models\SharedDebt;
use App\Models\User;

class SharedDebtPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SharedDebt $sharedDebt): bool
    {
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
    public function update(User $user, SharedDebt $sharedDebt): bool
    {
        return $sharedDebt->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SharedDebt $sharedDebt): bool
    {
        return $sharedDebt->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SharedDebt $sharedDebt): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SharedDebt $sharedDebt): bool
    {
        return false;
    }
}
