<x-app-layout>
    <x-form-container title="New Group">
        <form action="{{ route('groups.store') }}" method="POST" class="space-y-4">
            @csrf

            <x-input-with-label label="Group Name" name="name" value="{{ old('name') }}" type="text" step="0.01"
                required />
            <div>
                <label for="members" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add
                    Members</label>
                <select name="members[]" id="members"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white"
                    multiple required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-button>Create Group</x-button>
        </form>
    </x-form-container>
</x-app-layout>
