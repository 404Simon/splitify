<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Shopping Lists</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $group->name }}</p>
                </div>
                <x-enhanced-button href="{{ route('groups.shoppingLists.create', $group) }}" variant="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Shopping List
                </x-enhanced-button>
            </div>

            @if ($shoppingLists->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No shopping lists yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Create your first shopping list to get started.</p>
                        <x-enhanced-button href="{{ route('groups.shoppingLists.create', $group) }}" variant="primary">
                            Create Shopping List
                        </x-enhanced-button>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($shoppingLists as $list)
                        @php
                            $totalItems = $list->items->count();
                            $completedItems = $list->items->where('is_completed', true)->count();
                            $progressPercent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                        @endphp
                        <a href="{{ route('groups.shoppingLists.show', [$group, $list]) }}"
                            class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $list->name }}</h3>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Items</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $totalItems }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Completed</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">{{ $completedItems }}</span>
                                </div>
                                @if ($totalItems > 0)
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $progressPercent }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-green-500 dark:bg-green-600 h-2 rounded-full transition-all duration-300"
                                                style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                                    Created by {{ $list->creator->name }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
