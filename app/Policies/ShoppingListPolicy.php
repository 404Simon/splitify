<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ShoppingList;
use App\Models\User;

final class ShoppingListPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShoppingList $shoppingList): bool
    {
        return $shoppingList->group->users->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShoppingList $shoppingList): bool
    {
        return $shoppingList->group->users->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ShoppingList $shoppingList): bool
    {
        return $shoppingList->created_by === $user->id;
    }
}
