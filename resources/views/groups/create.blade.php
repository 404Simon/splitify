<x-app-layout>
    <div class="container mx-auto p-8 bg-white rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold mb-6">Create Group</h1>

        <form action="{{ route('groups.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-lg font-medium text-gray-700">Group Name</label>
                <input type="text" name="name" id="name"
                    class="mt-2 w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-6">
                <label for="members" class="block text-lg font-medium text-gray-700">Add Members</label>
                <select name="members[]" id="members"
                    class="mt-2 w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    multiple required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-200">Create
                Group</button>
        </form>
    </div>
</x-app-layout>
