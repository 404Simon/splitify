<x-app-layout>
    <x-form-container title="Edit {{ $group->name }}">
        <form action="{{ route('groups.update', $group->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <x-input-with-label label="Group Name" name="name" value="{{ old('name', $group->name) }}" type="text"
                required placeholder="Enter a name for your group" />
            <x-user-selector :selected-users="$selectedUsers" name="members" label="Group Members" :required="true" />
            <div
                class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 py-6 border-y border-gray-200 dark:border-gray-700">
                <x-enhanced-button variant="secondary" :href="route('groups.index', $group)">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </x-enhanced-button>
                <x-enhanced-button variant="primary" type="submit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </x-enhanced-button>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="mt-2 pt-6">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-700 dark:text-red-300 mb-4">
                    Deleting this group will permanently remove all associated data including shared debts,
                    transactions, recurring debts, invites, and map markers. This action cannot be undone.
                </p>

                <form id="deleteGroupForm" action="{{ route('groups.destroy', $group->id) }}" method="POST"
                    class="inline">
                    @csrf
                    @method('DELETE')

                    <div x-data>
                        <x-enhanced-button variant="danger" type="button"
                            x-on:click="$dispatch('open-modal', 'delete-group-modal')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Group
                        </x-enhanced-button>
                    </div>
                </form>
            </div>
        </div>
    </x-form-container>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal name="delete-group-modal" title="Delete {{ $group->name }}"
        description="Are you sure you want to delete this group? This action will permanently remove all associated data and cannot be undone."
        confirm-text="Delete" cancel-text="Cancel" variant="danger" form-id="deleteGroupForm" :items="[
            'All shared debts and their payment history',
            'All transactions between members',
            'All recurring debt schedules',
            'All pending invitations',
            'All map markers and locations',
            'All member associations',
        ]" />
</x-app-layout>
