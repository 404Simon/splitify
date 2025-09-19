<x-app-layout>
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="flex flex-col space-y-4 mb-6 sm:mb-8 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Map Markers</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $group->name }}</p>
                </div>
                <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
                    <x-enhanced-button variant="info" :href="route('groups.map.display', $group->id)">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3" />
                        </svg>
                        View Map
                    </x-enhanced-button>
                    @if (!$mapMarkers->isEmpty())
                        <x-enhanced-button variant="success" :href="route('groups.mapMarkers.create', $group->id)">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Marker
                        </x-enhanced-button>
                    @endif
                </div>
            </div>

            @if ($mapMarkers->isEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 sm:p-12 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No map markers yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm sm:text-base">Add locations that are
                        important to your group like meeting places or shared destinations.</p>
                    <x-enhanced-button variant="primary" :href="route('groups.mapMarkers.create', $group->id)">
                        Create Your First Map Marker
                    </x-enhanced-button>
                </div>
            @else
                <x-responsive-table :headers="['Name', 'Description', 'Address']">
                    <x-slot name="desktop">
                        @foreach ($mapMarkers as $marker)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @if ($marker->created_by === request()->user()->id)
                                            <x-enhanced-button size="sm" variant="info" :href="route('groups.mapMarkers.show', [$group->id, $marker->id])">
                                                View
                                            </x-enhanced-button>
                                            <x-enhanced-button size="sm" variant="primary" :href="route('groups.mapMarkers.edit', [$group->id, $marker->id])">
                                                Edit
                                            </x-enhanced-button>
                                        @else
                                            <x-enhanced-button size="sm" variant="info" :href="route('groups.mapMarkers.show', [$group->id, $marker->id])">
                                                View
                                            </x-enhanced-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>

                    <x-slot name="mobile">
                        <div class="space-y-4 p-4">
                            @foreach ($mapMarkers as $marker)
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $marker->emoji }} {{ $marker->name }}
                                            </h3>
                                            @if (!($marker->created_by === request()->user()->id))
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Created by {{ $marker->creator->name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($marker->description)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Description</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $marker->description }}
                                            </p>
                                        </div>
                                    @endif

                                    @if ($marker->address)
                                        <div class="mb-4">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Address</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $marker->address }}</p>
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap gap-2">
                                        <x-enhanced-button size="sm" variant="info" :href="route('groups.mapMarkers.show', [$group->id, $marker->id])">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </x-enhanced-button>
                                        @if ($marker->created_by === request()->user()->id)
                                            <x-enhanced-button size="sm" variant="primary" :href="route('groups.mapMarkers.edit', [$group->id, $marker->id])">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </x-enhanced-button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-slot>
                </x-responsive-table>
            @endif
        </div>
    </div>
</x-app-layout>
