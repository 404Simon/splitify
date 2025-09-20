<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Your Groups</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your expense groups</p>
                </div>

                @if (!$groups->isEmpty())
                    <x-enhanced-button href="{{ route('groups.create') }}" variant="primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Group
                    </x-enhanced-button>
                @endif
            </div>

            @if ($groups->isEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 sm:p-12 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No groups yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm sm:text-base">Create your first group to
                        start splitting expenses with friends and family.</p>
                    <x-enhanced-button href="{{ route('groups.create') }}" variant="primary">
                        Create Your First Group
                    </x-enhanced-button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($groups as $group)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                            <div class="p-4 sm:p-6">
                                <div class="space-y-4">
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('groups.show', $group->id) }}"
                                                class="text-xl font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                {{ $group->name }}
                                            </a>
                                            <div
                                                class="flex items-center space-x-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                <span>{{ $group->users->count() }}
                                                    {{ Str::plural('member', $group->users->count()) }}</span>
                                                @if ($group->created_by === Auth::id())
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400">
                                                        Admin
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($group->created_by === Auth::id())
                                            <x-enhanced-button href="{{ route('groups.edit', $group->id) }}"
                                                variant="warning">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </x-enhanced-button>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-3">Members:</p>
                                        <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
