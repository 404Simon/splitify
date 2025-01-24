<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Closure;

class UserDisplay extends Component
{
    public User $user;
    public bool $isGroupAdmin;

    /**
     * Create a new component instance.
     */
    public function __construct(User $user, bool $isGroupAdmin = false)
    {
        $this->user = $user;
        $this->isGroupAdmin = $isGroupAdmin;
    }

    public function isCurrentUser()
    {
        return $this->user->id === Auth::id();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-display');
    }
}
