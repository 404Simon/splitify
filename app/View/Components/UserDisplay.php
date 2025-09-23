<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

final class UserDisplay extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public User $user, public bool $isGroupAdmin = false) {}

    public function isCurrentUser(): bool
    {
        return $this->user->id === Auth::id();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.user-display');
    }
}
