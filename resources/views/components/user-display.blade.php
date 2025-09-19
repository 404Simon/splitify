<div
    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100 dark:border-gray-600 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
            <span class="text-blue-600 dark:text-blue-400 font-semibold text-lg">
                {{ substr($user->name, 0, 1) }}
            </span>
        </div>
        <div>
            <span class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
            <div class="flex items-center space-x-2 mt-1">
                @if ($isCurrentUser())
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                        You
                    </span>
                @endif
                @if ($isGroupAdmin)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                        Admin
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
