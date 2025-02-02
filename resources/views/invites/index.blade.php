<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
            Manage Invites for {{ $group->name }}
        </h1>
        <div class="mb-4">
            <div class="inline-flex space-x-4">
                <a href="{{ route('groups.invites.create', $group->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm font-medium rounded-md dark:bg-green-600 dark:hover:bg-green-700">
                    Create Invite
                </a>
            </div>
        </div>


        <div class="bg-white rounded-lg shadow p-6 mb-8 dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Pending Invites</h2>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($group->invites as $invite)
                    <li class="py-4" x-data="{ copied: false }">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-800 dark:text-white">
                                        {{ $invite->name ?? 'Invite' }}
                                    </span>
                                    @if ($invite->is_reusable)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Reusable
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Single Use
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center">
                                    <div class="text-gray-500 text-sm dark:text-gray-400 truncate mr-2"
                                        id="inviteLink-{{ $invite->id }}" x-ref="inviteLinkText">
                                        {{ route('invites.show', $invite->uuid) }}
                                    </div>
                                    <button
                                        class="text-indigo-500 hover:text-indigo-700 focus:outline-none dark:text-indigo-400 dark:hover:text-indigo-500"
                                        @click="copied = true; navigator.clipboard.writeText($refs.inviteLinkText.innerText); setTimeout(() => copied = false, 2000)"
                                        aria-label="Copy to clipboard"
                                        :title="copied ? 'Copied!' : 'Copy to clipboard'">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-4 h-4 inline-block align-middle" x-show="!copied">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7v8a2 2 0 002 2h6m-7-7l7-7m-7 7v4m-7-7H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-3m-4-3H5m6 0h6m-6 0l-3-3" />
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-4 h-4 inline-block align-middle text-green-500" x-show="copied"
                                            x-cloak>
                                            <path fill-rule="evenodd"
                                                d="M19.916 4.626a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06 0l-3.75-3.75a.75.75 0 111.06-1.06l3.22 3.22 6.97-6.97a.75.75 0 011.06 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-gray-500 text-xs mt-1 dark:text-gray-400">
                                    Expires on: <span class="font-semibold text-gray-800 dark:text-white">
                                        {{ \Carbon\Carbon::parse($invite->created_at)->addDays($invite->duration_days)->format('F j, Y') }}
                                    </span>
                                </div>
                            </div>
                            <form
                                action="{{ route('groups.invites.destroy', ['group' => $group->id, 'invite' => $invite->uuid]) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 ml-2 focus:outline-none dark:text-red-400 dark:hover:text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block align-middle"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500 dark:text-gray-400">No invites yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
