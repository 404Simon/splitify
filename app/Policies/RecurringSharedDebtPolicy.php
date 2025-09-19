<?php

namespace App\Policies;

use App\Models\RecurringSharedDebt;
use App\Models\User;

class RecurringSharedDebtPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RecurringSharedDebt $recurringSharedDebt): bool
    {
        return $recurringSharedDebt->group->users->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RecurringSharedDebt $recurringSharedDebt): bool
    {
        return $recurringSharedDebt->created_by === $user->id ||
               $recurringSharedDebt->group->users->where('pivot.role', 'admin')->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RecurringSharedDebt $recurringSharedDebt): bool
    {
        return $recurringSharedDebt->created_by === $user->id ||
               $recurringSharedDebt->group->users->where('pivot.role', 'admin')->contains($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RecurringSharedDebt $recurringSharedDebt): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RecurringSharedDebt $recurringSharedDebt): bool
    {
        return false;
    }
}
