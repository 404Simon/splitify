<x-app-layout>
    <x-form-container title="Create New Map Marker">
        <form action="{{ route('groups.mapMarkers.store', $group->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <x-input-with-label label="Name" name="name" value="{{ old('name') }}" type="text" required
                        max="50" autofocus placeholder="Enter marker name" />

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            placeholder="Optional description for this location">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="emoji" :value="__('Emoji')" />
                        <div class="mt-1">
                            <x-emoji-selector name="emoji" defaultEmoji="📍" :value="old('emoji', '📍')" />
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose an emoji to represent this location</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <x-input-with-label label="Address" name="address" value="{{ old('address') }}" type="text"
                        placeholder="Enter the address" />

                    <div class="bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-1">Location Tip</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Enter an address and we'll automatically find the coordinates for you. This helps mark the exact location on the map.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 py-6 border-t border-gray-200 dark:border-gray-700">
                <x-enhanced-button variant="secondary" :href="route('groups.mapMarkers.index', $group->id)">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </x-enhanced-button>
                <x-enhanced-button variant="primary" type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Map Marker
                </x-enhanced-button>
            </div>
        </form>
    </x-form-container>
</x-app-layout>
