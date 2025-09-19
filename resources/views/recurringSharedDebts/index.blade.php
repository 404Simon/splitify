<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Recurring Debts</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $group->name }}</p>
                </div>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                    <x-enhanced-button variant="secondary" :href="route('groups.show', $group)">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Group
                    </x-enhanced-button>

                    @if (!$recurringDebts->isEmpty())
                        <x-enhanced-button variant="primary" :href="route('groups.recurring-debts.create', $group)">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Recurring Debt
                        </x-enhanced-button>
                    @endif
                </div>
            </div>

            @if ($recurringDebts->isEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 sm:p-12 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No recurring debts yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm sm:text-base">Set up automatic recurring
                        debts for regular expenses like rent, utilities, or subscriptions.</p>
                    <x-enhanced-button variant="primary" :href="route('groups.recurring-debts.create', $group)">
                        Create Your First Recurring Debt
                    </x-enhanced-button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($recurringDebts as $debt)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                            <div class="p-4 sm:p-6">
                                <div class="space-y-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $debt->name }}</h3>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium mt-1
                                                {{ $debt->status === 'Active'
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                    : ($debt->status === 'Inactive'
                                                        ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                                        : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                                {{ $debt->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
                                            <p class="text-xl font-semibold text-gray-900 dark:text-white">
                                                €{{ number_format($debt->amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Frequency</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $debt->frequency_label }}</p>
                                        </div>
                                        <div class="col-span-2 sm:col-span-1">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Next
                                                Generation</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $debt->next_generation_date->format('M j, Y') }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Split
                                            between {{ $debt->users->count() }} {{ $debt->users->count() === 1 ? 'member' : 'members' }}:</p>
                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                            @foreach ($debt->users as $user)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                    {{ $user->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if ($debt->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $debt->description }}</p>
                                    @endif

                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Created by {{ $debt->creator->name }} on
                                        {{ $debt->created_at->format('M j, Y') }}
                                    </div>

                                    <div
                                        class="flex flex-wrap gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <x-enhanced-button size="default" variant="secondary" :href="route('groups.recurring-debts.show', [$group, $debt])">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </x-enhanced-button>

                                        @can('update', $debt)
                                            @if ($debt->is_active)
                                                <form
                                                    action="{{ route('groups.recurring-debts.generate-now', [$group, $debt]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <x-enhanced-button type="submit" size="default" variant="indigo">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Generate
                                                    </x-enhanced-button>
                                                </form>

                                                <form
                                                    action="{{ route('groups.recurring-debts.toggle-active', [$group, $debt]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-enhanced-button type="submit" size="default" variant="yellow">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M10 9v6m4-6v6" />
                                                        </svg>
                                                        Pause
                                                    </x-enhanced-button>
                                                </form>
                                            @else
                                                <form
                                                    action="{{ route('groups.recurring-debts.toggle-active', [$group, $debt]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-enhanced-button type="submit" size="default" variant="green">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M12 5v.01M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                        </svg>
                                                        Resume
                                                    </x-enhanced-button>
                                                </form>
                                            @endif

                                            <x-enhanced-button size="default" variant="secondary" :href="route('groups.recurring-debts.edit', [$group, $debt])">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </x-enhanced-button>
                                        @endcan
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
