<x-app-layout>
    <div class="container mx-auto p-8 bg-white rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold mb-6">Edit Group</h1>

        <form action="{{ route('groups.update', $group->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-lg font-medium text-gray-700">Group Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}"
                    class="mt-2 w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-6">
                <label for="members" class="block text-lg font-medium text-gray-700">Add Members</label>
                <select name="members[]" id="members"
                    class="mt-2 w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    multiple required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if (in_array($user->id, $group->users->pluck('id')->toArray())) selected @endif>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-200">Update
                Group</button>
        </form>
        <form action="{{ route('groups.destroy', $group->id) }}" class="mt-2" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="w-full py-3 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-200">Delete
                Group</button>
        </form>
    </div>
</x-app-layout>
