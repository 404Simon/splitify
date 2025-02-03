<x-app-layout>
    <x-form-container title="Edit Map Marker">
        <form action="{{ route('groups.mapMarkers.update', [$group->id, $mapMarker->id]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <x-input-with-label label="Name" name="name" value="{{ old('name', $mapMarker->name) }}" type="text" required max="50" />
            <x-input-with-label label="Description" name="description" value="{{ old('description', $mapMarker->description) }}" type="text" />
            <x-input-with-label label="Address" name="address" value="{{ old('address', $mapMarker->address) }}" type="text" />
            <x-input-with-label label="Latitude" name="lat" value="{{ old('lat', $mapMarker->lat) }}" type="number" step="any" />
            <x-input-with-label label="Longitude" name="lon" value="{{ old('lon', $mapMarker->lon) }}" type="number" step="any" />
            <x-input-with-label label="Emoji" name="emoji" value="{{ old('emoji', $mapMarker->emoji) }}" type="text" />

            <x-button>Update Map Marker</x-button>
            <x-button type="button" onclick="window.location='{{ route('groups.mapMarkers.index', $group->id) }}'">Cancel</x-button>
        </form>
    </x-form-container>
</x-app-layout>
