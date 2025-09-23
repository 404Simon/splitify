<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MapMarker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class MapMarkerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MapMarker $mapMarker): bool
    {
        // Check if user is a member of the group the marker belongs to
        return $mapMarker->group->users->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MapMarker $mapMarker): bool
    {
        // Allow if user is the creator of the marker
        if ($user->id === $mapMarker->created_by) {
            return true;
        }

        // Allow if user is the admin (creator) of the group
        return $user->id === $mapMarker->group->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MapMarker $mapMarker): bool
    {
        // Use same logic as update
        return $this->update($user, $mapMarker);
    }
}
