<div class="user-display bg-white p-4 rounded-lg shadow-md flex items-center space-x-4">
    <div>
        <h3 class="font-semibold text-lg text-gray-800">{{ $user->name }}</h3>
    </div>
    @if ($isCurrentUser())
        <span class="text-green-500 font-semibold text-sm">You</span>
    @endif
    @if ($isGroupAdmin)
        <span class="text-red-500 font-semibold text-sm">Admin</span>
    @endif
</div>
