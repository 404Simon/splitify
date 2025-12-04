<x-app-layout>
    <div class="py-6" x-data="shoppingListManager()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('groups.shoppingLists.index', $group) }}"
                            class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $shoppingList->name }}</h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 ml-9">{{ $group->name }}</p>
                </div>
                <div class="flex gap-2">
                    @can('delete', $shoppingList)
                        <form id="deleteShoppingListForm" action="{{ route('groups.shoppingLists.destroy', [$group, $shoppingList]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-enhanced-button type="button" variant="danger" size="sm"
                                x-on:click="$dispatch('open-modal', 'delete-shopping-list-modal')"
                                data-testid="delete-list-button">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </x-enhanced-button>
                        </form>
                    @endcan
                    @can('update', $shoppingList)
                        <x-enhanced-button href="{{ route('groups.shoppingLists.edit', [$group, $shoppingList]) }}" variant="secondary" size="sm" data-testid="edit-list-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </x-enhanced-button>
                    @endcan
                </div>
            </div>

            <!-- Add Item Form -->
            @can('update', $shoppingList)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <form action="{{ route('groups.shoppingLists.items.store', [$group, $shoppingList]) }}" method="POST" class="flex gap-3" data-testid="add-item-form">
                        @csrf
                        <input type="hidden" name="showCompleted" x-bind:value="showCompleted ? '1' : '0'">
                        <div class="flex-1">
                            <input type="text" name="name" placeholder="Add an item..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                required autofocus>
                        </div>
                        <x-enhanced-button type="submit" variant="primary" aria-label="Add item" data-testid="add-item-button">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </x-enhanced-button>
                    </form>
                </div>
            @endcan

            <!-- Toggle Completed Items Button -->
            @if ($shoppingList->items->isNotEmpty())
                <div class="mb-4 flex justify-end">
                    <button @click="toggleCompleted"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span x-text="showCompleted ? 'Hide Completed' : 'Show Completed'"></span>
                    </button>
                </div>
            @endif

            <!-- Shopping List Items -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                @if ($shoppingList->items->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No items yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Add your first item to get started.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($shoppingList->items as $item)
                            <div x-show="showCompleted || !{{ $item->is_completed ? 'true' : 'false' }}"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                
                                <form action="{{ route('groups.shoppingLists.items.toggle', [$group, $shoppingList, $item]) }}" method="POST" data-testid="toggle-item-form-{{ $item->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="showCompleted" x-bind:value="showCompleted ? '1' : '0'">
                                    <button type="submit" class="flex-shrink-0" aria-label="Toggle item {{ $item->name }}" data-testid="toggle-item-{{ $item->id }}">
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all
                                            {{ $item->is_completed 
                                                ? 'bg-green-500 border-green-500 dark:bg-green-600 dark:border-green-600' 
                                                : 'border-gray-300 dark:border-gray-600 hover:border-green-500 dark:hover:border-green-500' }}">
                                            @if ($item->is_completed)
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </div>
                                    </button>
                                </form>

                                <span class="flex-1 text-gray-900 dark:text-white {{ $item->is_completed ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">
                                    {{ $item->name }}
                                </span>

                                @can('update', $shoppingList)
                                    <form action="{{ route('groups.shoppingLists.items.destroy', [$group, $shoppingList, $item]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="showCompleted" x-bind:value="showCompleted ? '1' : '0'">
                                        <button type="submit" aria-label="Delete item {{ $item->name }}"
                                            class="flex-shrink-0 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($shoppingList->items->isNotEmpty())
                <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    @php
                        $totalItems = $shoppingList->items->count();
                        $completedItems = $shoppingList->items->where('is_completed', true)->count();
                    @endphp
                    {{ $completedItems }} of {{ $totalItems }} items completed
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        @can('delete', $shoppingList)
            <x-confirmation-modal name="delete-shopping-list-modal"
                title="Delete {{ $shoppingList->name }}"
                description="Are you sure you want to delete this shopping list? This action cannot be undone."
                confirm-text="Delete List" cancel-text="Cancel" variant="danger"
                form-id="deleteShoppingListForm" />
        @endcan
    </div>

    <script>
        function shoppingListManager() {
            return {
                showCompleted: new URLSearchParams(window.location.search).get('showCompleted') === 'true',
                
                toggleCompleted() {
                    this.showCompleted = !this.showCompleted;
                    const url = new URL(window.location);
                    if (this.showCompleted) {
                        url.searchParams.set('showCompleted', 'true');
                    } else {
                        url.searchParams.delete('showCompleted');
                    }
                    window.history.pushState({}, '', url);
                }
            }
        }
    </script>
</x-app-layout>
