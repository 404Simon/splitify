<div class="bg-white p-3 rounded-md shadow-sm flex items-center justify-between text-sm text-gray-700">
    <div class="flex items-center space-x-2">
       <span class="font-medium">{{ $user->name }}</span>
         @if ($isCurrentUser())
            <span class="bg-green-100 text-green-600 font-semibold px-2 py-0.5 rounded-full text-xs">You</span>
        @endif
        @if ($isGroupAdmin)
            <span class="bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full text-xs">Admin</span>
        @endif
    </div>
</div>
