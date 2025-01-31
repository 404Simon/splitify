<x-app-layout>
    <x-form-container title="New Invite">
        <form action="{{ route('groups.invites.store', $group->id) }}" method="POST" class="space-y-4">
            @csrf
            <x-input-with-label label="Duration Days" name="duration_days" value="{{ old('duration_days', 1) }}"
                type="number" step="1" required min=1 max=30 />
            <x-input-with-label label="Note" name="name" value="{{ old('name') }}" type="text" max=128 />
            <x-checkbox-with-label label="Reusable" name="is_reusable" checked="{{ old('is_reusable, 1') }}" />
            <x-button>Create Invite</x-button>
        </form>
    </x-form-container>
</x-app-layout>
