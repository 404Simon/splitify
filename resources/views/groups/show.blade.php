<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold">
            {{ $group->name }}
        </h1>
        <p class="text-gray-600 font-medium mt-2">Members:</p>
        <x-user-list-display :users="$group->users" :groupAdminId="$group->created_by" />
</x-app-layout>
