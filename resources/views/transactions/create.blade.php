<x-app-layout>
    <x-form-container title="Add Transaction to {{ $group->name }}">
        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="group_id" value="{{ $group->id }}">
            <input type="hidden" name="payer_id" value="{{ auth()->id() }}">

            <div>
                <label for="recipient_id"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient</label>
                <select name="recipient_id" id="recipient_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white">
                    <option value="">Select Recipient</option>
                    @foreach ($group->users as $user)
                        @if ($user->id !== auth()->id())
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <x-input-with-label label="Amount (€)" name="amount" type="number" step="0.01" required />
            <x-input-with-label label="Description" name="description" type="text" id="transactionDescription"
                required />

            <x-button>Add Transaction</x-button>
        </form>
    </x-form-container>
</x-app-layout>
