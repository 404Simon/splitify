<x-app-layout>
    <x-form-container title="New Map Marker">
        <form action="{{ route('groups.mapMarkers.store', $group->id) }}" method="POST" class="space-y-4">
            @csrf
            <x-input-with-label label="Name" name="name" value="{{ old('name') }}" type="text" required max="50" />
            <x-input-with-label label="Description" name="description" value="{{ old('description') }}" type="text" />
            <x-input-with-label label="Address" name="address" value="{{ old('address') }}" type="text" />
            <x-input-with-label label="Emoji" name="emoji" value="{{ old('emoji', '📍') }}" type="text" />

            <x-button>Create Map Marker</x-button>
        </form>
    </x-form-container>
</x-app-layout>
