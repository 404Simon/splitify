<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Your Groups</h1>
            <a href="{{ route('groups.create') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition">
                {{-- Styled "New Group" button --}}
                New Group
            </a>
        </div>

        @if ($groups->isEmpty())
            <p class="text-gray-600 text-lg dark:text-gray-400">You are not a member of any groups yet.</p>
        @else
            <ul class="space-y-4">
                @foreach ($groups as $group)
                    <li
                        class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('groups.show', $group->id) }}"
                                class="text-xl font-semibold text-indigo-600 hover:underline dark:text-indigo-500">
                                {{ $group->name }}
                            </a>
                            @if ($group->created_by === request()->user()->id)
                                <a href="{{ route('groups.edit', $group->id) }}"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-400 dark:hover:bg-yellow-500 dark:focus:ring-yellow-400 transition">
                                    Edit
                                </a>
                            @endif
                        </div>
                        <p class="text-gray-700 font-medium mt-2 dark:text-gray-300">Members:</p>
                        <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
