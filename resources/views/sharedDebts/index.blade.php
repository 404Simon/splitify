<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">All Shared Debts</h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $group->name }}</p>
            </div>
            <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                <x-enhanced-button href="{{ route('groups.show', $group->id) }}" variant="secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Group
                </x-enhanced-button>
                <x-enhanced-button href="{{ route('groups.sharedDebts.create', $group->id) }}" variant="danger">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Debt
                </x-enhanced-button>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
            @if ($sharedDebts->isEmpty())
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No shared debts yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Start tracking shared expenses with your
                        group members.</p>
                    <x-enhanced-button href="{{ route('groups.sharedDebts.create', $group->id) }}" variant="danger">
                        Create Your First Shared Debt
                    </x-enhanced-button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($sharedDebts as $debt)
                        <div
                            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $debt->name }}</h3>
                                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                                        €{{ number_format($debt->amount, 2) }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Created by {{ $debt->creator->name }} •
                                        {{ $debt->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                                @if ($debt->created_by === Auth::id())
                                    <div class="flex flex-wrap gap-2">
                                        <x-enhanced-button href="{{ route('groups.sharedDebts.edit', ['group' => $group->id, 'sharedDebt' => $debt->id]) }}" variant="secondary" size="sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </x-enhanced-button>
                                        <form
                                            action="{{ route('groups.sharedDebts.destroy', ['group' => $group->id, 'sharedDebt' => $debt->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-enhanced-button type="submit" variant="danger" size="sm"
                                                onclick="return confirm('Are you sure you want to delete this debt?')">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </x-enhanced-button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Split between
                                    {{ count($debt->getUserShares()) }} member(s):</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($debt->getUserShares() as $share)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                            {{ $share['user']->name }}: €{{ number_format($share['amount'], 2) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
