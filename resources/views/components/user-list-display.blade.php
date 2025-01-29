<div class="divide-y divide-gray-200 rounded-md overflow-hidden dark:divide-gray-700 dark:bg-gray-800 dark:text-white">
    @foreach ($users as $user)
        <x-user-display :user="$user" :isGroupAdmin="$user->isGroupAdmin" :isCurrentUser="$user->id === Auth::id()" />
    @endforeach
</div>
