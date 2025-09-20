<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class UserSelector extends Component
{
    public function __construct(
        public Collection $selectedUsers = new Collection,
        public string $name = 'users',
        public string $label = 'Group Members',
        public bool $required = false,
        public ?int $currentUserId = null,
    ) {
        $this->currentUserId = $this->currentUserId ?? auth()->id();
    }

    public function render(): View|Closure|string
    {
        return view('components.user-selector');
    }
}
