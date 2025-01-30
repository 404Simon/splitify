<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
            {{ $group->name }}
        </h1>

        <div class="py-12" x-data="{
            inviteLink: '',
            showInviteLinkSection: false,
            copyToClipboard() { navigator.clipboard.writeText(this.inviteLink).then(() => { alert('Invite link copied to clipboard!'); }); },
            generateInviteLink() {
                showInviteLinkSection = false;
                inviteLink = '';
                fetch('/groups/{{ $group->id }}/generate-invite', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(response => response.json()).then(data => {
                    this.inviteLink = data.inviteLink;
                    this.showInviteLinkSection = true;
                });
            }
        }">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Group Name: {{ $group->name }}</h3>

                        <button x-on:click="generateInviteLink()"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-400 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:ring focus:ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4">
                            Generate Invite Link
                        </button>

                        <div x-show="showInviteLinkSection" id="inviteLinkSection" class="mt-4">
                            <p class="font-semibold">Invite Link:</p>
                            <div id="inviteLinkDisplay" class="bg-gray-100 p-2 rounded-md break-all"
                                x-text="inviteLink">
                            </div>
                            <button x-on:click="copyToClipboard()" id="copyInviteLinkButton"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mt-2">
                                Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="mb-4">
            <div class="inline-flex space-x-4">
                <a href="{{ route('groups.sharedDebts.create', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-indigo-600 dark:hover:bg-indigo-700">
                    Add Debt
                </a>
                <a href="{{ route('groups.transactions.create', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-md dark:bg-green-600 dark:hover:bg-green-700">
                    Add Transaction
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Members</h2>
            <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Debts Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($group->users as $user)
                    <div class="bg-gray-50 p-4 rounded-md shadow dark:bg-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 dark:text-white">{{ $user->name }}</h3>
                        <ul class="space-y-2">
                            @foreach ($userDebts[$user->id] ?? [] as $otherUserId => $amount)
                                @php
                                    $otherUser = $group->users->find($otherUserId);
                                    $formattedAmount = number_format(abs($amount), 2);
                                @endphp
                                @if ($amount > 0)
                                    <li class="text-green-600 dark:text-green-400">
                                        Owes {{ $otherUser->name }} <span
                                            class="font-semibold">€{{ $formattedAmount }}</span>
                                    </li>
                                @elseif($amount < 0)
                                    <li class="text-red-600 dark:text-red-400">
                                        Is owed by {{ $otherUser->name }} <span
                                            class="font-semibold">€{{ $formattedAmount }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Shared Debts</h2>
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
                                    <form action="{{ route('sharedDebts.destroy', $debt->id) }}" method="POST">
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
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Transactions</h2>
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
                            @if ($transaction->payer_id === Auth::id())
                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
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
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500 dark:text-gray-400">No transactions yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
