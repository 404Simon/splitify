<div
    class="bg-white p-3 rounded-md shadow-sm flex items-center justify-between text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-300">
    <div class="flex items-center space-x-2">
        <span class="font-medium dark:text-white">{{ $user->name }}</span>
        @if ($isCurrentUser())
            <span
                class="bg-green-100 text-green-600 font-semibold px-2 py-0.5 rounded-full text-xs dark:bg-green-700 dark:text-green-100">You</span>
        @endif
        @if ($isGroupAdmin)
            <span
                class="bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full text-xs dark:bg-red-700 dark:text-red-100">Admin</span>
        @endif
    </div>
</div>
