<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
            {{ $group->name }}
        </h1>

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
                        <div class="flex justify-between items-start mb-2">
                             <div>
                                <span class="font-semibold text-gray-800">{{ $debt->name }}</span>
                                 <span class="text-gray-600"> - €{{ number_format($debt->amount, 2) }}</span>
                             </div>
                            <div class="text-gray-500 text-sm">
                                Created by: <span class="font-medium">{{ $debt->creator->name }}</span>
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

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Shared Debt</h2>
            <form action="{{ route('sharedDebts.store') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Debt Name</label>
                    <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 text-sm font-medium mb-2">Amount (€)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="members" class="block text-gray-700 text-sm font-medium mb-2">Split Between</label>
                    <div class="mt-2 space-y-2">
                        @foreach($group->users as $user)
                            <div class="flex items-center">
                                <input
                                    class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    type="checkbox"
                                    name="members[]"
                                    value="{{ $user->id }}"
                                    id="user-{{ $user->id }}"
                                >
                                <label class="ml-2 text-gray-700" for="user-{{ $user->id }}">
                                    {{ $user->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Debt</button>
            </form>
        </div>
    </div>
</x-app-layout>
