<x-app-layout>
    <x-form-container title="Edit {{ $shoppingList->name }}">
        <form action="{{ route('groups.shoppingLists.update', [$group, $shoppingList]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <x-input-with-label label="Name" name="name" type="text" value="{{ $shoppingList->name }}" required />
            <x-enhanced-button variant="primary" type="submit">Update Shopping List</x-enhanced-button>
        </form>
    </x-form-container>
</x-app-layout>
