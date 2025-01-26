<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
            {{ $group->name }}
        </h1>

        <div class="mb-4">
            <div class="inline-flex space-x-4">
                <a href="{{ route('groups.sharedDebts.create', $group->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    Add Debt
                </a>
                <a href="{{ route('groups.transactions.create', $group->id) }}" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                    Add Transaction
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Members</h2>
             <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
        </div>

       <div class="bg-white rounded-lg shadow p-6 mb-8">
           <h2 class="text-xl font-semibold text-gray-800 mb-4">Debts Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($group->users as $user)
               <div class="bg-gray-50 p-4 rounded-md shadow">
                   <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $user->name }}</h3>
                    <ul class="space-y-2">
                        @foreach($userDebts[$user->id] ?? [] as $otherUserId => $amount)
                            @php
                                $otherUser = $group->users->find($otherUserId);
                                $formattedAmount = number_format(abs($amount), 2);
                            @endphp
                            @if($amount > 0)
                                <li class="text-green-600">
                                    Owes {{ $otherUser->name }} <span class="font-semibold">€{{ $formattedAmount }}</span>
                                </li>
                            @elseif($amount < 0)
                                <li class="text-red-600">
                                    Is owed by {{ $otherUser->name }} <span class="font-semibold">€{{ $formattedAmount }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
           @endforeach
        </div>
       </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8">
             <h2 class="text-xl font-semibold text-gray-800 mb-4">Shared Debts</h2>
            <ul class="divide-y divide-gray-200">
                @forelse($group->sharedDebts as $debt)
                    <li class="py-4">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $debt->name }}</span>
                                <span class="text-gray-600"> - €{{ number_format($debt->amount, 2) }}</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-gray-500 text-sm">
                                     Created by: <span class="font-medium">{{ $debt->creator->name }}</span>
                                </div>
                                @if($debt->created_by === Auth::id())
                                <form action="{{ route('sharedDebts.destroy', $debt->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block align-middle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                       <div class="flex flex-wrap gap-2 mt-2">
                             @foreach($debt->getUserShares() as $share)
                                <span class="bg-yellow-100 text-yellow-800  font-medium text-sm py-1 px-2 rounded-full">{{ $share['user']->name }}: €{{ $share['amount'] }}</span>
                            @endforeach
                        </div>
                   </li>
                @empty
                    <li class="px-6 py-4 text-gray-500">No shared debts yet.</li>
                @endforelse
            </ul>
        </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Transactions</h2>
            <ul class="divide-y divide-gray-200">
                @forelse($group->transactions as $transaction)
                    <li class="py-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $transaction->description ?? 'Transaction' }}</span>
                                <span class="text-gray-600"> - €{{ number_format($transaction->amount, 2) }}</span>
                                <div class="text-gray-500 text-sm">
                                    Payer: <span class="font-medium">{{ $transaction->payer->name }}</span> to Recipient: <span class="font-medium">{{ $transaction->recipient->name }}</span>
                                </div>
                            </div>
                            @if($transaction->payer_id === Auth::id())
                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 ml-2 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block align-middle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                             @endif
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500">No transactions yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
