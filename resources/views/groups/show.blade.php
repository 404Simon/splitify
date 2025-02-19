<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
            {{ $group->name }}
        </h1>
        <div class="mb-4">
            <div class="inline-flex space-x-4">

                <a href="{{ route('groups.mapMarkers.index', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-700 text-white text-sm font-medium rounded-md dark:bg-indigo-600 dark:hover:bg-indigo-700">
                    🗺️ Map
                </a>

            </div>
        </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Debts Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($group->users as $user)
                    <div class="bg-gray-50 p-4 rounded-md shadow dark:bg-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 dark:text-white">{{ $user->name }}</h3>
                        <ul class="space-y-2">
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
                                    <li class="text-red-600 dark:text-red-400">
                                        Owes {{ $otherUser->name }} <span
                                            class="font-semibold">€{{ $formattedAmount }}</span>
                                    </li>
                                    @php $totalOwing += $amount; @endphp
                                @elseif($amount < 0)
                                    <li class="text-green-600 dark:text-green-400">
                                        Is owed by {{ $otherUser->name }} <span
                                            class="font-semibold">€{{ $formattedAmount }}</span>
                                    </li>
                                    @php $totalOwed -= $amount; @endphp
                                @endif
                            @endforeach
                        </ul>

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Total Owed: <span class="{{ $totalOwed > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">€{{ number_format($totalOwed, 2) }}</span>
                            </p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Total Owing: <span class="{{ $totalOwing > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">€{{ number_format($totalOwing, 2) }}</span>
                            </p>
                            @if($totalOwed > $totalOwing)
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Net Balance: <span class="text-green-600 dark:text-green-400">€{{ number_format($totalOwed - $totalOwing, 2) }}</span>
                                </p>
                            @elseif($totalOwing > $totalOwed)
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Net Balance: <span class="text-red-600 dark:text-red-400">€{{ number_format($totalOwing - $totalOwed, 2) }}</span>
                                </p>
                            @else
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Net Balance: <span class="text-gray-600 dark:text-gray-400">€0.00</span>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Shared Debts</h2>
                <a href="{{ route('groups.sharedDebts.create', $group->id) }}"
                    class="inline-flex items-center justify-center p-2 bg-red-600 hover:bg-red-800 text-white text-sm font-medium rounded-full dark:bg-red-700 dark:hover:bg-red-800 w-8 h-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($group->sharedDebts as $debt)
                    <li class="py-4">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $debt->name }}</span>
                                <span class="text-gray-600 dark:text-gray-400"> -
                                    €{{ number_format($debt->amount, 2) }}</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-gray-500 text-sm dark:text-gray-400">
                                    Created by: <span
                                        class="font-medium dark:text-white">{{ $debt->creator->name }}</span>
                                </div>
                                @if ($debt->created_by === Auth::id())
                                    <a href="{{ route('groups.sharedDebts.edit', ['group' => $group->id, 'sharedDebt' => $debt->id]) }}"
                                        class="text-blue-500 hover:text-blue-700 focus:outline-none dark:text-blue-400 dark:hover:text-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 inline-block align-middle">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('groups.sharedDebts.destroy', ['group' => $group->id, 'sharedDebt' => $debt->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 focus:outline-none dark:text-red-400 dark:hover:text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 inline-block align-middle" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($debt->getUserShares() as $share)
                                <span
                                    class="bg-yellow-100 text-yellow-800  font-medium text-sm py-1 px-2 rounded-full dark:bg-yellow-700 dark:text-yellow-100">{{ $share['user']->name }}:
                                    €{{ $share['amount'] }}</span>
                            @endforeach
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500 dark:text-gray-400">No shared debts yet.</li>
                @endforelse
            </ul>
        </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Transactions</h2>
                <a href="{{ route('groups.transactions.create', $group->id) }}"
                    class="inline-flex items-center justify-center p-2 bg-emerald-500 hover:bg-emerald-700 text-white text-sm font-medium rounded-full dark:bg-emerald-600 dark:hover:bg-emerald-700 w-8 h-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($group->transactions as $transaction)
                    <li class="py-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="font-semibold text-gray-800 dark:text-white">{{ $transaction->description ?? 'Transaction' }}</span>
                                <span class="text-gray-600 dark:text-gray-400"> -
                                    €{{ number_format($transaction->amount, 2) }}</span>
                                <div class="text-gray-500 text-sm dark:text-gray-400">
                                    Payer: <span
                                        class="font-medium dark:text-white">{{ $transaction->payer->name }}</span> to
                                    Recipient: <span
                                        class="font-medium dark:text-white">{{ $transaction->recipient->name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if ($transaction->payer_id === Auth::id())
                                    <a href="{{ route('groups.transactions.edit', ['group' => $group->id, 'transaction' => $transaction->id]) }}"
                                        class="text-blue-500 hover:text-blue-700 focus:outline-none dark:text-blue-400 dark:hover:text-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 inline-block align-middle">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('groups.transactions.destroy', ['group' => $group->id, 'transaction' => $transaction->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 ml-2 focus:outline-none dark:text-red-400 dark:hover:text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 inline-block align-middle" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500 dark:text-gray-400">No transactions yet.</li>
                @endforelse
            </ul>
        </div>
                <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Members</h2>
                 @if ($group->created_by === Auth::id())
                    <a href="{{ route('groups.invites.index', $group->id) }}"
                        class="inline-flex items-center justify-center p-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-full dark:bg-blue-600 dark:hover:bg-blue-700 w-8 h-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    </a>
                 @endif
            </div>
            <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
        </div>

    </div>
</x-app-layout>
