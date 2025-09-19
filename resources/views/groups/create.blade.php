<x-app-layout>
    <x-form-container title="New Group">
        <form action="{{ route('groups.store') }}" method="POST" class="space-y-4">
            @csrf

            <x-input-with-label label="Group Name" name="name" value="{{ old('name') }}" type="text" step="0.01"
                required />
            <x-enhanced-button variant="primary" type="submit">Create Group</x-enhanced-button>
        </form>
    </x-form-container>
</x-app-layout>
