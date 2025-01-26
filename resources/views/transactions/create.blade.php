<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
            Add New Transaction to {{ $group->name }}
        </h1>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Transaction</h2>
            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <input type="hidden" name="payer_id" value="{{ auth()->id() }}">

                <div>
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700">Recipient</label>
                    <select name="recipient_id" id="recipient_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="">Select Recipient</option>
                        @foreach($group->users as $user)
                            @if($user->id !== auth()->id())
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (€)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                 <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (optional)</label>
                    <input type="text" name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add Transaction</button>
            </form>
        </div>
    </div>
</x-app-layout>
