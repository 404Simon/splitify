<x-app-layout>
    <x-form-container title="Edit Debt for {{ $group->name }}">
        <form action="{{ route('groups.sharedDebts.update', [$group->id, $sharedDebt->id]) }}" method="POST"
            class="space-y-4">
            @csrf
            @method('PUT')

            <x-input-with-label label="Name" name="name" type="text" step="0.01" required
                value="{{ old('name', $sharedDebt->name) }}" />
            <x-input-with-label label="Amount (€)" name="amount" type="number" step="0.01" required
                value="{{ old('amount', $sharedDebt->amount) }}" />
            <div>
                <label for="members" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Split
                    Between</label>
                <div class="mt-2 space-y-2">
                    @foreach ($group->users as $user)
                        <div class="flex items-center">
                            <input
                                class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2"
                                type="checkbox" name="members[]" value="{{ $user->id }}"
                                id="user-{{ $user->id }}" @if (
                                    (is_array(old('members')) && in_array($user->id, old('members'))) ||
                                        (!is_array(old('members')) && in_array($user->id, $sharedDebt->users->pluck('id')->toArray()))) checked @endif>
                            <label class="ml-2 text-gray-700 dark:text-gray-300" for="user-{{ $user->id }}">
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <x-enhanced-button variant="primary" type="submit">Update Debt</x-enhanced-button>
        </form>
    </x-form-container>
</x-app-layout>
