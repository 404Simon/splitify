<x-app-layout>
    <x-form-container title="Add Debt to {{ $group->name }}">
        <form action="{{ route('groups.sharedDebts.store', $group->id) }}" method="POST" class="space-y-4">
            @csrf
            <x-input-with-label label="Name" name="name" type="text" step="0.01" required />
            <x-input-with-label label="Amount (€)" name="amount" type="number" step="0.01" required />
            <div>
                <label for="members" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Split
                    Between</label>
                <div class="mt-2 space-y-2">
                    @foreach ($group->users as $user)
                        <div class="flex items-center">
                            <input
                                class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2"
                                type="checkbox" name="members[]" value="{{ $user->id }}" checked
                                id="user-{{ $user->id }}">
                            <label class="ml-2 text-gray-700 dark:text-gray-300" for="user-{{ $user->id }}">
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <x-enhanced-button variant="primary" type="submit">Add Debt</x-enhanced-button>
        </form>
    </x-form-container>
</x-app-layout>
