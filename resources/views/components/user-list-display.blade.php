<div class="space-y-3">
    @foreach ($users as $user)
        <x-user-display :user="$user" :isGroupAdmin="$user->isGroupAdmin" :isCurrentUser="$user->id === Auth::id()" />
    @endforeach
</div>
