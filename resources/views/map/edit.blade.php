<x-app-layout>
    <x-form-container title="Edit Map Marker">
        <form action="{{ route('groups.mapMarkers.update', [$group->id, $mapMarker->id]) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <x-input-with-label label="Name" name="name" value="{{ old('name', $mapMarker->name) }}" type="text"
                        required max="50" placeholder="Enter marker name" />

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            placeholder="Optional description for this location">{{ old('description', $mapMarker->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="emoji" :value="__('Emoji')" />
                        <div class="mt-1">
                            <x-emoji-selector name="emoji" :value="old('emoji', $mapMarker->emoji)" />
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose an emoji to represent this location</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <x-input-with-label label="Address" name="address" value="{{ old('address', $mapMarker->address) }}"
                        type="text" placeholder="Enter the address" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input-with-label label="Latitude" name="lat" value="{{ old('lat', $mapMarker->lat) }}"
                            type="number" step="any" placeholder="0.000000" />
                        <x-input-with-label label="Longitude" name="lon" value="{{ old('lon', $mapMarker->lon) }}"
                            type="number" step="any" placeholder="0.000000" />
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">Location Tip</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    When you update the address, the coordinates will be automatically calculated. You can also manually adjust the coordinates if needed.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 py-6 border-y border-gray-200 dark:border-gray-700">
                <x-enhanced-button variant="secondary" :href="route('groups.mapMarkers.show', [$group->id, $mapMarker->id])">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </x-enhanced-button>
                <x-enhanced-button variant="info" type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Map Marker
                </x-enhanced-button>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="mt-2 pt-6">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-700 dark:text-red-300 mb-4">
                    Deleting this map marker will permanently remove it from the group. This action cannot be undone.
                </p>

                <form id="deleteMapMarkerForm" action="{{ route('groups.mapMarkers.destroy', [$group->id, $mapMarker->id]) }}"
                    method="POST" class="inline">
                    @csrf
                    @method('DELETE')

                    <div x-data>
                        <x-enhanced-button variant="danger" type="button"
                            x-on:click="$dispatch('open-modal', 'delete-map-marker-modal')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Map Marker
                        </x-enhanced-button>
                    </div>
                </form>
            </div>
        </div>
    </x-form-container>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal name="delete-map-marker-modal" title="Delete {{ $mapMarker->name }}"
        description="Are you sure you want to delete this map marker? This action cannot be undone."
        confirm-text="Delete" cancel-text="Cancel" variant="danger" form-id="deleteMapMarkerForm" />
</x-app-layout>
