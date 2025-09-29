<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex flex-col space-y-4 mb-8 sm:flex-row sm:items-start sm:justify-between sm:space-y-0">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            @if($mapMarker->emoji)
                                {{ $mapMarker->emoji }} 
                            @endif
                            {{ $mapMarker->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">{{ $group->name }}</p>
                    </div>
                    @can('update', $mapMarker)
                        <div class="flex-shrink-0">
                            <x-enhanced-button href="{{ route('groups.mapMarkers.edit', [$group->id, $mapMarker->id]) }}" variant="info">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Map Marker
                            </x-enhanced-button>
                        </div>
                    @endcan
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        @if($mapMarker->description)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
                                <p class="text-gray-700 dark:text-gray-300">{{ $mapMarker->description }}</p>
                            </div>
                        @endif

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Location</h2>
                            <div class="space-y-2">
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-gray-700 dark:text-gray-300 break-words">{{ $mapMarker->address }}</p>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 ml-7">
                                    <span class="font-medium">Coordinates:</span> {{ number_format($mapMarker->lat, 6) }}, {{ number_format($mapMarker->lon, 6) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Details</h2>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Created by</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $mapMarker->creator->name }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Created</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $mapMarker->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Last updated</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $mapMarker->updated_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
