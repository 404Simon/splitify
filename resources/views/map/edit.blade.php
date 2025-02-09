<x-app-layout>
    <x-form-container title="Edit Map Marker">
        <form action="{{ route('groups.mapMarkers.update', [$group->id, $mapMarker->id]) }}" method="POST"
            class="space-y-4">
            @csrf
            @method('PUT')
            <x-input-with-label label="Name" name="name" value="{{ old('name', $mapMarker->name) }}" type="text"
                required max="50" />
            <x-input-with-label label="Description" name="description"
                value="{{ old('description', $mapMarker->description) }}" type="text" />
            <x-input-with-label label="Address" name="address" value="{{ old('address', $mapMarker->address) }}"
                type="text" />
            <x-input-with-label label="Latitude" name="lat" value="{{ old('lat', $mapMarker->lat) }}" type="number"
                step="any" />
            <x-input-with-label label="Longitude" name="lon" value="{{ old('lon', $mapMarker->lon) }}"
                type="number" step="any" />
            <x-input-with-label label="Emoji" name="emoji" value="{{ old('emoji', $mapMarker->emoji) }}"
                type="text" />

            <x-button>Update Map Marker</x-button>
            <form class="inline-block" action="{{ route('groups.mapMarkers.destroy', [$group->id, $mapMarker->id]) }}"
                method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 bg-red-600 hover:bg-red-700 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-600">
                    Delete Marker
                </button>
            </form>

        </form>
    </x-form-container>
</x-app-layout>
