<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Recurring Debts</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $group->name }}</p>
                </div>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('groups.show', $group) }}"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Group
                    </a>
                    <a href="{{ route('groups.recurring-debts.create', $group) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Recurring Debt
                    </a>
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
                    <a href="{{ route('groups.recurring-debts.create', $group) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        Create Your First Recurring Debt
                    </a>
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
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $debt->name }}</h3>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
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
                                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Amount</p>
                                            <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                                €{{ number_format($debt->amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Frequency</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $debt->frequency_label }}</p>
                                        </div>
                                        <div class="col-span-2 sm:col-span-1">
                                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Next
                                                Generation</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $debt->next_generation_date->format('M j, Y') }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-2">Split
                                            between {{ $debt->users->count() }} member(s):</p>
                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                            @foreach ($debt->users as $user)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                    {{ $user->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if ($debt->description)
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                            {{ $debt->description }}</p>
                                    @endif

                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Created by {{ $debt->creator->name }} on
                                        {{ $debt->created_at->format('M j, Y') }}
                                    </div>

                                    <div
                                        class="flex flex-wrap gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <a href="{{ route('groups.recurring-debts.show', [$group, $debt]) }}"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="hidden sm:inline">View Details</span>
                                            <span class="sm:hidden">View</span>
                                        </a>

                                        @can('update', $debt)
                                            @if ($debt->is_active)
                                                <form
                                                    action="{{ route('groups.recurring-debts.generate-now', [$group, $debt]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:hover:bg-indigo-900/40 transition-colors"
                                                        title="Generate now">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        <span class="hidden sm:inline">Generate</span>
                                                        <span class="sm:hidden">Gen</span>
                                                    </button>
                                                </form>

                                                <form
                                                    action="{{ route('groups.recurring-debts.toggle-active', [$group, $debt]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition-colors"
                                                        title="Pause">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M10 9v6m4-6v6" />
                                                        </svg>
                                                        <span class="hidden sm:inline">Pause</span>
                                                        <span class="sm:hidden">⏸</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form
                                                    action="{{ route('groups.recurring-debts.toggle-active', [$group, $debt]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 transition-colors"
                                                        title="Resume">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M12 5v.01M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                        </svg>
                                                        <span class="hidden sm:inline">Resume</span>
                                                        <span class="sm:hidden">▶</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('groups.recurring-debts.edit', [$group, $debt]) }}"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span class="hidden sm:inline">Edit</span>
                                                <span class="sm:hidden">✏</span>
                                            </a>
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
