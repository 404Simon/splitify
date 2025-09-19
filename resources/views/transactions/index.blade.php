<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">All Transactions</h1>
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
                <x-enhanced-button href="{{ route('groups.transactions.create', $group->id) }}" variant="success">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Transaction
                </x-enhanced-button>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
            @if ($transactions->isEmpty())
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No transactions yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Record payments between group members to
                        settle debts.</p>
                    <x-enhanced-button href="{{ route('groups.transactions.create', $group->id) }}" variant="success">
                        Create Your First Transaction
                    </x-enhanced-button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($transactions as $transaction)
                        <div
                            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $transaction->description ?? 'Payment' }}
                                    </h3>
                                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                        €{{ number_format($transaction->amount, 2) }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $transaction->payer->name }} → {{ $transaction->recipient->name }} •
                                        {{ $transaction->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                                @if ($transaction->payer_id === Auth::id())
                                    <div class="flex flex-wrap gap-2">
                                        <x-enhanced-button href="{{ route('groups.transactions.edit', ['group' => $group->id, 'transaction' => $transaction->id]) }}" variant="secondary" size="sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </x-enhanced-button>
                                        <form
                                            action="{{ route('groups.transactions.destroy', ['group' => $group->id, 'transaction' => $transaction->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-enhanced-button type="submit" variant="danger" size="sm"
                                                onclick="return confirm('Are you sure you want to delete this transaction?')">
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
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
