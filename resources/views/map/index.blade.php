<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
            Map Markers for {{ $group->name }}
        </h1>
        <div class="mb-4 flex justify-between items-center">
            <div>
                <a href="{{ route('groups.map.display', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-blue-600 dark:hover:bg-blue-700">
                    View Map
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Map Markers</h2>
                <a href="{{ route('groups.mapMarkers.create', $group->id) }}"
                    class="inline-flex items-center justify-center p-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-full dark:bg-green-600 dark:hover:bg-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>

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
                            <th scope="col" class="relative px-6 py-3 text-right"> {{-- Align actions to the right --}}
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($mapMarkers as $marker)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $marker->emoji }} {{ $marker->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($marker->description, 50) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($marker->address, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex justify-end space-x-2">
                                    {{-- Align buttons to the right --}}
                                    @if ($marker->created_by === request()->user()->id)
                                        <div class="flex space-x-2"> {{-- Keep buttons together and space them --}}
                                            <a href="{{ route('groups.mapMarkers.show', [$group->id, $marker->id]) }}"
                                                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-blue-600 dark:hover:bg-blue-700">
                                                View
                                            </a>
                                            <a href="{{ route('groups.mapMarkers.edit', [$group->id, $marker->id]) }}"
                                                class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-700 text-white text-sm font-medium rounded-md dark:bg-indigo-600 dark:hover:bg-indigo-700">
                                                Edit
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-gray-500 text-sm dark:text-gray-400 flex items-center justify-between"
                                            style="width: 100%;"> {{-- Use justify-between to push text to left --}}
                                            <div class="text-left"> {{-- Explicitly align text to the left --}}
                                                Created by: <span
                                                    class="font-medium dark:text-white">{{ $marker->creator->name }}</span>
                                            </div>
                                            <div class="flex justify-end"> {{-- Ensure view button is on the right --}}
                                                <a href="{{ route('groups.mapMarkers.show', [$group->id, $marker->id]) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-blue-600 dark:hover:bg-blue-700">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center"
                                    colspan="4"> {{-- Center the "No map markers" text --}}
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
