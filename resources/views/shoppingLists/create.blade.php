<x-app-layout>
    <x-form-container title="Create Shopping List for {{ $group->name }}">
        <form action="{{ route('groups.shoppingLists.store', $group) }}" method="POST" class="space-y-4">
            @csrf
            <x-input-with-label label="Name" name="name" type="text" required autofocus />
            <x-enhanced-button variant="primary" type="submit">Create Shopping List</x-enhanced-button>
        </form>
    </x-form-container>
</x-app-layout>
