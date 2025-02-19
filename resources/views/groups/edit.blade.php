<x-app-layout>
    <x-form-container title="Edit {{ $group->name }}">
        <form action="{{ route('groups.update', $group->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <x-input-with-label label="Group Name" name="name" value="{{ old('name', $group->name) }}" type="text"
                step="0.01" required />
            <div>
                <label for="members" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add
                    Members</label>
                <select name="members[]" id="members"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white"
                    multiple required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if (in_array($user->id, $group->users->pluck('id')->toArray())) selected @endif>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <x-button>Update Group</x-button>
        </form>
        <form action="{{ route('groups.destroy', $group->id) }}" class="mt-4" method="POST">
            @csrf
            @method('DELETE')
            <x-button variant="danger">Delete Group</x-button>
        </form>
    </x-form-container>
</x-app-layout>
