<x-app-layout>
    <x-form-container title="Edit Transaction for {{ $group->name }}">
        <form action="{{ route('groups.transactions.update', [$group->id, $transaction->id]) }}" method="POST"
            class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="recipient_id"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient</label>
                <select name="recipient_id" id="recipient_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white">
                    <option value="">Select Recipient</option>
                    @foreach ($group->users as $user)
                        @if ($user->id !== auth()->id())
                            <option value="{{ $user->id }}"
                                {{ old('recipient_id', $transaction->recipient_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('recipient_id')
                    <x-input-error messages="{{ $message }}" class="mt-2" />
                @enderror
            </div>

            <x-input-with-label label="Amount (€)" name="amount" type="number" step="0.01" required
                value="{{ old('amount', $transaction->amount) }}" />
            <x-input-with-label label="Description" name="description" type="text"
                value="{{ old('description', $transaction->description) }}" />
            <x-enhanced-button variant="primary" type="submit">Update Transaction</x-enhanced-button>
        </form>
    </x-form-container>
</x-app-layout>
