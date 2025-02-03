<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
            Map Markers for {{ $group->name }}
        </h1>
        <div class="mb-4">
            <div class="inline-flex space-x-4">
                <a href="{{ route('groups.mapMarkers.create', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-md dark:bg-green-600 dark:hover:bg-green-700">
                    Add Map Marker
                </a>
                <a href="{{ route('groups.map.display', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-blue-600 dark:hover:bg-blue-700">
                    View Map
                </a>
                <a href="{{ route('groups.show', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white text-sm font-medium rounded-md dark:bg-gray-600 dark:hover:bg-gray-700">
                    Back to Group
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Map Markers</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Address
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($mapMarkers as $marker)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $marker->emoji }} {{ $marker->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($marker->description, 50) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($marker->address, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex space-x-2">
                                    <a href="{{ route('groups.mapMarkers.show', [$group->id, $marker->id]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-blue-600 dark:hover:bg-blue-700">
                                        View
                                    </a>
                                    <a href="{{ route('groups.mapMarkers.edit', [$group->id, $marker->id]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-700 text-white text-sm font-medium rounded-md dark:bg-indigo-600 dark:hover:bg-indigo-700">
                                        Edit
                                    </a>
                                    <form class="inline-block"
                                        action="{{ route('groups.mapMarkers.destroy', [$group->id, $marker->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="ml-2 inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 text-white text-sm font-medium rounded-md dark:bg-red-600 dark:hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                    colspan="4">
                                    No map markers added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
