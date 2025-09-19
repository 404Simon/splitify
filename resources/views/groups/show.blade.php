<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $group->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $group->users->count() }}
                        {{ $group->users->count() === 1 ? 'member' : 'members' }}</p>
                </div>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                    <x-enhanced-button href="{{ route('groups.recurring-debts.index', $group) }}" variant="indigo">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Recurring Debts
                    </x-enhanced-button>
                    <x-enhanced-button href="{{ route('groups.mapMarkers.index', $group->id) }}" variant="secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Map
                    </x-enhanced-button>
                </div>
            </div>

            <!-- Debts Overview -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Balance Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($group->users as $user)
                        <div
                            class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ $user->name }}
                            </h3>
                            <div class="space-y-2">
                                @php
                                    $totalOwed = 0;
                                    $totalOwing = 0;
                                @endphp
                                @foreach ($userDebts[$user->id] ?? [] as $otherUserId => $amount)
                                    @php
                                        $otherUser = $group->users->find($otherUserId);
                                        $formattedAmount = number_format(abs($amount), 2);
                                    @endphp
                                    @if ($amount > 0)
                                        <div class="text-sm text-red-600 dark:text-red-400">
                                            Owes {{ $otherUser->name }} <span
                                                class="font-semibold">€{{ $formattedAmount }}</span>
                                        </div>
                                        @php $totalOwing += $amount; @endphp
                                    @elseif($amount < 0)
                                        <div class="text-sm text-green-600 dark:text-green-400">
                                            Is owed by {{ $otherUser->name }} <span
                                                class="font-semibold">€{{ $formattedAmount }}</span>
                                        </div>
                                        @php $totalOwed -= $amount; @endphp
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 space-y-1">
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Total Owed: <span
                                        class="{{ $totalOwed > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }} font-medium">€{{ number_format($totalOwed, 2) }}</span>
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Total Owing: <span
                                        class="{{ $totalOwing > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500' }} font-medium">€{{ number_format($totalOwing, 2) }}</span>
                                </div>
                                @if ($totalOwed > $totalOwing)
                                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                                        Net: +€{{ number_format($totalOwed - $totalOwing, 2) }}
                                    </div>
                                @elseif($totalOwing > $totalOwed)
                                    <div class="text-sm font-semibold text-red-600 dark:text-red-400">
                                        Net: -€{{ number_format($totalOwing - $totalOwed, 2) }}
                                    </div>
                                @else
                                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                        Net: €0.00
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Active Recurring Debts -->
            @if ($group->recurringSharedDebts->where('is_active', true)->isNotEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
                    <div
                        class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Active Recurring Debts</h2>
                        <a href="{{ route('groups.recurring-debts.index', $group) }}"
                            class="inline-flex items-center text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors">
                            View All
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($group->recurringSharedDebts->where('is_active', true)->take(3) as $recurringDebt)
                            <a href="{{ route('groups.recurring-debts.show', [$group, $recurringDebt]) }}"
                                class="block bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate pr-2">
                                        {{ $recurringDebt->name }}</h3>
                                    <span
                                        class="text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300 px-2 py-1 rounded-full flex-shrink-0">
                                        {{ $recurringDebt->frequency_label }}
                                    </span>
                                </div>
                                <p class="text-lg font-bold text-purple-600 dark:text-purple-400 mb-2">
                                    €{{ number_format($recurringDebt->amount, 2) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    Next: {{ $recurringDebt->next_generation_date->format('M j') }}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($recurringDebt->users->take(3) as $user)
                                        <span
                                            class="inline-block w-6 h-6 bg-purple-200 dark:bg-purple-700 rounded-full text-xs flex items-center justify-center text-purple-800 dark:text-purple-200">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    @endforeach
                                    @if ($recurringDebt->users->count() > 3)
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400 ml-1">+{{ $recurringDebt->users->count() - 3 }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif


            <!-- Shared Debts -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
                <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Shared Debts</h2>
                    @if (!$group->sharedDebts->isEmpty())
                        <x-enhanced-button href="{{ route('groups.sharedDebts.create', $group->id) }}"
                            variant="danger">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Debt
                        </x-enhanced-button>
                    @else
                        <x-enhanced-button href="{{ route('groups.sharedDebts.create', $group->id) }}" variant="danger"
                            class="hidden sm:inline-flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Debt
                        </x-enhanced-button>
                    @endif
                </div>

                @if ($group->sharedDebts->isEmpty())
                    <div class="text-center py-8">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No shared debts yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Start tracking shared expenses with
                            your group members.</p>
                        <x-enhanced-button href="{{ route('groups.sharedDebts.create', $group->id) }}"
                            variant="danger">
                            Create Your First Shared Debt
                        </x-enhanced-button>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($group->sharedDebts->sortByDesc('created_at')->take(3) as $debt)
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
                                            <x-enhanced-button
                                                href="{{ route('groups.sharedDebts.edit', ['group' => $group->id, 'sharedDebt' => $debt->id]) }}"
                                                variant="secondary" size="sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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
                                        {{ count($debt->getUserShares()) }}
                                        {{ count($debt->getUserShares()) === 1 ? 'member' : 'members' }}:</p>
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

                        @if ($group->sharedDebts->count() > 5)
                            <div class="text-center pt-4">
                                <a href="{{ route('groups.sharedDebts.index', $group->id) }}"
                                    class="inline-flex items-center text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                    View all {{ $group->sharedDebts->count() }} shared debts
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Transactions -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
                <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                    @if (!$group->transactions->isEmpty())
                        <x-enhanced-button href="{{ route('groups.transactions.create', $group->id) }}"
                            variant="success">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Transaction
                        </x-enhanced-button>
                    @else
                        <x-enhanced-button href="{{ route('groups.transactions.create', $group->id) }}"
                            variant="success" class="hidden sm:inline-flex">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Transaction
                        </x-enhanced-button>
                    @endif
                </div>

                @if ($group->transactions->isEmpty())
                    <div class="text-center py-8">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No transactions yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Record payments between group members
                            to settle debts.</p>
                        <x-enhanced-button href="{{ route('groups.transactions.create', $group->id) }}"
                            variant="success">
                            Create Your First Transaction
                        </x-enhanced-button>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($group->transactions->sortByDesc('created_at')->take(3) as $transaction)
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
                                            <x-enhanced-button
                                                href="{{ route('groups.transactions.edit', ['group' => $group->id, 'transaction' => $transaction->id]) }}"
                                                variant="secondary" size="sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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

                        @if ($group->transactions->count() > 5)
                            <div class="text-center pt-4">
                                <a href="{{ route('groups.transactions.index', $group->id) }}"
                                    class="inline-flex items-center text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">
                                    View all {{ $group->transactions->count() }} transactions
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <!-- Members -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Group Members</h2>
                    @if ($group->created_by === Auth::id())
                        <x-enhanced-button href="{{ route('groups.invites.index', $group->id) }}" variant="info">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Invite Member
                        </x-enhanced-button>
                    @endif
                </div>
                <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
            </div>

        </div>
    </div>
</x-app-layout>
