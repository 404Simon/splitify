<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
                Map Marker: {{ $mapMarker->name }}
            </h1>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2 dark:text-white">Group</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $group->name }}</p>
            </div>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2 dark:text-white">Description</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $mapMarker->description ?? 'No description provided.' }}
                </p>
            </div>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2 dark:text-white">Location</h2>
                <p class="text-gray-700 dark:text-gray-300">Address: {{ $mapMarker->address }}</p>
                <p class="text-gray-700 dark:text-gray-300">
                    Latitude: {{ $mapMarker->lat }}, Longitude: {{ $mapMarker->lon }}
                </p>
            </div>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2 dark:text-white">Created At</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $mapMarker->created_at->format('F j, Y, g:i a') }}</p>
            </div>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2 dark:text-white">Updated At</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $mapMarker->updated_at->format('F j, Y, g:i a') }}</p>
            </div>

            @can('update', $mapMarker)
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                <a href="{{ route('groups.mapMarkers.edit', [$group->id, $mapMarker->id]) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium rounded-md dark:bg-blue-600 dark:hover:bg-blue-700">
                    Edit Map Marker
                </a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
