<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 dark:text-white">
            Manage Invites for {{ $group->name }}
        </h1>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-8 dark:bg-gray-800">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-2 sm:space-y-0">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Pending Invites</h2>
                <x-enhanced-button href="{{ route('groups.invites.create', $group->id) }}" variant="success" class="min-h-[44px] px-4 py-2 self-start sm:self-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">Add Invite</span>
                </x-enhanced-button>
            </div>

            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($group->invites as $invite)
                    <li class="py-4" x-data="{ copied: false }">
                        <div class="space-y-3 sm:space-y-0">
                            <!-- Header row with name, badges, and delete button -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-2 sm:space-y-0">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 flex-wrap">
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
                                    <div class="text-gray-500 text-xs mt-1 dark:text-gray-400">
                                        Expires on: <span class="font-semibold text-gray-800 dark:text-white">
                                            {{ \Carbon\Carbon::parse($invite->created_at)->addDays($invite->duration_days)->format('F j, Y') }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Delete button - positioned for easy mobile access -->
                                <form
                                    action="{{ route('groups.invites.destroy', ['group' => $group->id, 'invite' => $invite->uuid]) }}"
                                    method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <x-enhanced-button type="submit" variant="danger" size="sm" class="min-h-[44px] min-w-[44px]" title="Delete invite">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-enhanced-button>
                                </form>
                            </div>
                            
                            <!-- Invite link row - optimized for mobile copying -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 min-w-0">
                                         <div class="text-gray-700 dark:text-gray-300 text-sm font-mono break-all"
                                             id="inviteLink-{{ $invite->uuid }}" x-ref="inviteLinkText">
                                            {{ route('invites.show', $invite->uuid) }}
                                        </div>
                                    </div>
                                    <button
                                        class="flex-shrink-0 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-800 dark:hover:bg-indigo-700 text-indigo-700 dark:text-indigo-300 rounded-lg p-2 sm:px-3 sm:py-2 min-h-[44px] w-[44px] sm:w-auto sm:min-w-[80px] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                        @click="copied = true; navigator.clipboard.writeText($refs.inviteLinkText.innerText); setTimeout(() => copied = false, 2000)"
                                        aria-label="Copy to clipboard"
                                        :title="copied ? 'Copied!' : 'Copy invite link'">
                                        <div class="flex items-center justify-center sm:space-x-1 w-full">
                                            <div class="relative w-4 h-4 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor"
                                                     class="w-4 h-4 absolute" x-show="!copied">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.031.754-.031 1.127 0C12.489 4.006 13.5 4.97 13.5 6.108V7.5M15 13.5H9v-3h6v3zM15 13.5v5.25c0 .621-.504 1.125-1.125 1.125H6.375c-.621 0-1.125-.504-1.125-1.125V13.5M15 13.5l2.25-2.25M9 13.5L6.75 11.25" />
                                                </svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                     class="w-4 h-4 text-green-600 dark:text-green-400 absolute" x-show="copied"
                                                     x-cloak>
                                                    <path fill-rule="evenodd"
                                                          d="M19.916 4.626a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06 0l-3.75-3.75a.75.75 0 111.06-1.06l3.22 3.22 6.97-6.97a.75.75 0 011.06 0z"
                                                          clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="text-xs font-medium hidden sm:inline whitespace-nowrap" x-text="copied ? 'Copied!' : 'Copy'"></span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-gray-500 dark:text-gray-400">No invites yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
