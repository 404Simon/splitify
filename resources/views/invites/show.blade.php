<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
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
            <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
                <p class="mb-4 text-gray-700 dark:text-gray-300">
                    Do you want to accept the invitation to join the group <span
                        class="font-semibold">{{ $invite->group->name }}</span>?
                </p>
                <div class="flex space-x-4">
                    <form method="POST" action="{{ route('invites.accept', $invite->uuid) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-md dark:bg-green-600 dark:hover:bg-green-700">
                            Accept
                        </button>
                    </form>
                    <form method="POST" action="{{ route('invites.deny', $invite->uuid) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-700 text-white text-sm font-medium rounded-md dark:bg-red-600 dark:hover:bg-red-700">
                            Decline
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
