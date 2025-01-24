<div>
    <ul class="list-disc pl-5 space-y-1">
        @foreach ($users as $user)
            <x-user-display :user="$user" :isGroupAdmin="$user->isGroupAdmin" />
        @endforeach
    </ul>

</div>
