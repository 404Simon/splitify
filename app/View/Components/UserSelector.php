<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

final class UserSelector extends Component
{
    public function __construct(
        public Collection $selectedUsers = new Collection,
        public string $name = 'users',
        public string $label = 'Group Members',
        public bool $required = false,
        public ?int $currentUserId = null,
    ) {
        $this->currentUserId ??= auth()->id();
    }

    public function render(): View
    {
        return view('components.user-selector');
    }
}
