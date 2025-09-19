<x-app-layout>
    <div class="container mx-auto px-4 py-6 sm:py-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 dark:text-white leading-tight">
            You've been invited to join {{ $invite->group->name }}
        </h1>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-700 dark:border-red-700 dark:text-red-100"
                role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-700 dark:border-green-700 dark:text-green-100"
                role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (!session('error'))
            <div class="bg-white rounded-lg shadow p-4 sm:p-6 dark:bg-gray-800">
                <p class="mb-6 text-gray-700 dark:text-gray-300 text-base sm:text-lg leading-relaxed">
                    Do you want to accept the invitation to join the group <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $invite->group->name }}</span>?
                </p>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-4">
                    <form method="POST" action="{{ route('invites.accept', $invite->uuid) }}" class="flex-1">
                        @csrf
                        <x-enhanced-button type="submit" variant="success" class="w-full min-h-[48px] text-base font-medium">
                            Accept Invitation
                        </x-enhanced-button>
                    </form>
                    <form method="POST" action="{{ route('invites.deny', $invite->uuid) }}" class="flex-1">
                        @csrf
                        <x-enhanced-button type="submit" variant="danger" class="w-full min-h-[48px] text-base font-medium">
                            Decline Invitation
                        </x-enhanced-button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
