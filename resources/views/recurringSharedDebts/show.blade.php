<x-app-layout>
    <div class="py-4 sm:py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 sm:mb-8">
                <div class="mb-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $recurringDebt->name }}
                    </h1>
                    <p class="text-base sm:text-gray-600 dark:text-gray-400 mt-1 text-gray-700">Recurring Debt in
                        {{ $group->name }}</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-start">
                    <x-enhanced-button variant="secondary" :href="route('groups.recurring-debts.index', $group)">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Recurring Debts
                    </x-enhanced-button>
                    @can('update', $recurringDebt)
                        <x-enhanced-button variant="primary" :href="route('groups.recurring-debts.edit', [$group, $recurringDebt])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </x-enhanced-button>
                    @endcan
                </div>
            </div>

            <div class="space-y-6 lg:grid lg:grid-cols-3 lg:gap-8 lg:space-y-0">
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                                        €{{ number_format($recurringDebt->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Frequency</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $recurringDebt->frequency_label }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $recurringDebt->status === 'Active'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                            : ($recurringDebt->status === 'Inactive'
                                                ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                        {{ $recurringDebt->status }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Next Generation</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $recurringDebt->next_generation_date->format('M j, Y') }}</p>
                                </div>
                            </div>

                            @if ($recurringDebt->description)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Description</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $recurringDebt->description }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Members
                                ({{ $recurringDebt->users->count() }})</h2>
                            <div class="space-y-3">
                                @foreach ($recurringDebt->getUserShares() as $share)
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                                                    {{ substr($share['user']->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white text-base sm:text-sm">
                                                    {{ $share['user']->name }}</p>
                                                @if ($share['user']->id === $recurringDebt->created_by)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Creator</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900 dark:text-white text-base sm:text-sm">
                                                €{{ $share['amount'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">per
                                                {{ strtolower($recurringDebt->frequency_label) === 'monthly' ? 'month' : strtolower($recurringDebt->frequency_label) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if ($recurringDebt->generatedDebts->isNotEmpty())
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="p-4 sm:p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    Generated Debts ({{ $recurringDebt->generatedDebts->count() }})
                                </h2>
                                <div class="space-y-3">
                                    @foreach ($recurringDebt->generatedDebts->take(10) as $debt)
                                        <div
                                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white text-base sm:text-sm">
                                                    {{ $debt->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $debt->created_at->format('M j, Y \a\t g:i A') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p
                                                    class="font-semibold text-gray-900 dark:text-white text-base sm:text-sm">
                                                    €{{ number_format($debt->amount, 2) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $debt->users->count() }} members</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($recurringDebt->generatedDebts->count() > 10)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">
                                            And {{ $recurringDebt->generatedDebts->count() - 10 }} more...
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    @can('update', $recurringDebt)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="p-4 sm:p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                                <div class="space-y-3">
                                    @if ($recurringDebt->is_active)
                                        <form
                                            action="{{ route('groups.recurring-debts.generate-now', [$group, $recurringDebt]) }}"
                                            method="POST">
                                            @csrf
                                            <x-enhanced-button type="submit" variant="primary" fullWidth="true">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Generate Now
                                            </x-enhanced-button>
                                        </form>

                                        <form
                                            action="{{ route('groups.recurring-debts.toggle-active', [$group, $recurringDebt]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <x-enhanced-button type="submit" variant="warning" fullWidth="true">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                         d="M10 9v6m4-6v6" />
                                                 </svg>
                                                Pause
                                            </x-enhanced-button>
                                        </form>
                                    @else
                                        <form
                                            action="{{ route('groups.recurring-debts.toggle-active', [$group, $recurringDebt]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <x-enhanced-button type="submit" variant="success" fullWidth="true">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M12 5v.01M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                </svg>
                                                Resume
                                            </x-enhanced-button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endcan

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Start Date</p>
                                    <p class="text-base sm:text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $recurringDebt->start_date->format('M j, Y') }}</p>
                                </div>
                                @if ($recurringDebt->end_date)
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">End Date</p>
                                        <p class="text-base sm:text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $recurringDebt->end_date->format('M j, Y') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                                    <p class="text-base sm:text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $recurringDebt->creator->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Created On</p>
                                    <p class="text-base sm:text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $recurringDebt->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('delete', $recurringDebt)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-800">
                            <div class="p-4 sm:p-6">
                                <h3 class="text-lg font-semibold text-red-900 dark:text-red-400 mb-4">Danger Zone</h3>
                                <form action="{{ route('groups.recurring-debts.destroy', [$group, $recurringDebt]) }}"
                                    method="POST" id="deleteRecurringDebtForm" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <div x-data>
                                    <x-enhanced-button variant="danger" type="button" fullWidth="true"
                                        x-on:click="$dispatch('open-modal', 'delete-recurring-debt-modal')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Recurring Debt
                                    </x-enhanced-button>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal name="delete-recurring-debt-modal" title="Delete {{ $recurringDebt->name }}"
        description="Are you sure you want to delete this recurring debt? This will delete the recurring debt but keep all generated debts."
        confirm-text="Delete" cancel-text="Cancel" variant="danger" form-id="deleteRecurringDebtForm" />
</x-app-layout>
