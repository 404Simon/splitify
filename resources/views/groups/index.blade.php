<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Your Groups</h1>
            <a href="{{ route('groups.create') }}"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                New Group
            </a>
        </div>

        @if ($groups->isEmpty())
            <p class="text-gray-600 text-lg">You are not a member of any groups yet.</p>
        @else
            <ul class="space-y-4">
                @foreach ($groups as $group)
                    <li class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('groups.show', $group->id) }}"
                                class="text-xl font-semibold text-blue-600 hover:underline">
                                {{ $group->name }}
                            </a>
                            @if ($group->created_by === request()->user()->id)
                                <a href="{{ route('groups.edit', $group->id) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition">
                                    Edit
                                </a>
                            @endif
                        </div>
                        <p class="text-gray-600 font-medium mt-2">Members:</p>
                        <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
