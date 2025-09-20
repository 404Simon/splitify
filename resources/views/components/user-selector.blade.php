@props([
    'selectedUsers' => collect(),
    'name' => 'users',
    'label' => 'Group Members',
    'required' => false,
    'currentUserId' => null,
])

@php
    $componentId = 'user-selector-' . uniqid();
    $hiddenInputId = $componentId . '-hidden';
    $currentUserId = $currentUserId ?? auth()->id();
@endphp

<div x-data="{
    selectedIds: @js($selectedUsers->pluck('id')->toArray()),

    get selectedUsers() {
        return @js($selectedUsers->toArray()).filter(user => this.selectedIds.includes(user.id));
    },

    removeUser(userId) {
        if (userId === {{ $currentUserId }}) return; // Prevent removing admin
        this.selectedIds = this.selectedIds.filter(id => id !== userId);
        this.updateHiddenInputs();
    },

    updateHiddenInputs() {
        const container = document.getElementById('{{ $hiddenInputId }}');
        container.innerHTML = '';
        this.selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '{{ $name }}[]';
            input.value = id;
            container.appendChild(input);
        });
    },

    isCurrentUser(userId) {
        return userId === {{ $currentUserId }};
    }
}" class="space-y-4">
    <!-- Label -->
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Current Members Display -->
    <div class="space-y-2">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Current Members (<span x-text="selectedUsers.length"></span>)
        </div>
        <div class="space-y-2">
            <template x-for="user in selectedUsers" :key="user.id">
                <div
                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/40 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm"
                                x-text="user.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                    x-text="user.name"></span>
                                <span x-show="isCurrentUser(user.id)"
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                    Admin
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="user.email"></div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <!-- Tooltip for admin user -->
                        <div x-show="isCurrentUser(user.id)" class="relative group">
                            <button type="button" disabled
                                class="p-1 text-gray-300 dark:text-gray-600 cursor-not-allowed rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <!-- Tooltip -->
                            <div
                                class="absolute bottom-full right-0 mb-2 px-3 py-2 text-xs text-white bg-gray-900 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-10">
                                Admin cannot remove themselves
                                <div
                                    class="absolute top-full right-3 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900">
                                </div>
                            </div>
                        </div>
                        <!-- Remove button for other users -->
                        <button x-show="!isCurrentUser(user.id)" type="button" @click="removeUser(user.id)"
                            :data-test="'remove-user-' + user.id"
                            class="p-1 text-red-400 hover:text-red-600 dark:text-red-300 dark:hover:text-red-100 transition-colors rounded-full hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Info Message about Invite Links -->
    <x-callout type="info" title="Add New Members">
        To add new members to this group, use the <strong>invite links</strong> feature. You can create and manage
        invites from the group page.
    </x-callout>

    <!-- Hidden Inputs -->
    <div id="{{ $hiddenInputId }}" style="display: none;">
        @foreach ($selectedUsers as $user)
            <input type="hidden" name="{{ $name }}[]" value="{{ $user->id }}">
        @endforeach
    </div>

    <!-- Error Display -->
    @error($name)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
