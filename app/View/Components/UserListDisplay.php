<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Closure;

class UserListDisplay extends Component
{
    public $users;

    /**
     * Create a new component instance.
     */
    public function __construct($users, $groupAdminId)
    {
        $this->users = $users->map(function ($user) use ($groupAdminId) {
            $user->isGroupAdmin = $user->id === $groupAdminId;
            return $user;
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-list-display');
    }
}
