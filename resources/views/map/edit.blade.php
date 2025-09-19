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
            <div>
                <x-input-label for="emoji" :value="__('Emoji')" />
                <div class="mt-1">
                    <x-emoji-selector name="emoji" :value="old('emoji', $mapMarker->emoji)" />
                </div>
            </div>

            <x-enhanced-button type="submit">Update Map Marker</x-enhanced-button>
        </form>
        <form class="inline-block mt-2" action="{{ route('groups.mapMarkers.destroy', [$group->id, $mapMarker->id]) }}"
            method="POST">
            @csrf
            @method('DELETE')
            <x-enhanced-button type="submit" variant="danger">
                Delete Marker
            </x-enhanced-button>
        </form>

    </x-form-container>
</x-app-layout>
