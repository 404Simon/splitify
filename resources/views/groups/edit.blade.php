<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit Group</h1>

            <form action="{{ route('groups.update', $group->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Group
                        Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white"
                        {{-- Consistent input style --}} required>
                </div>

                <div>
                    <label for="members" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add
                        Members</label>
                    <select name="members[]" id="members"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white"
                        {{-- Consistent select style --}} multiple required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if (in_array($user->id, $group->users->pluck('id')->toArray())) selected @endif>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">Update
                    Group</button>
            </form>

            <form action="{{ route('groups.destroy', $group->id) }}" class="mt-4" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-600">Delete
                    Group</button>
            </form>
        </div>
    </div>
</x-app-layout>
