<x-app-layout>
    <x-form-container title="Create Recurring Debt for {{ $group->name }}">
        <form action="{{ route('groups.recurring-debts.store', $group) }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-6 sm:grid sm:grid-cols-1 md:grid-cols-2 sm:gap-6 sm:space-y-0">
                <x-input-with-label label="Name" name="name" type="text" placeholder="e.g., Monthly Rent"
                    required />
                <x-input-with-label label="Amount (€)" name="amount" type="number" step="0.01" min="0.01"
                    required />
            </div>

            <div class="space-y-6 sm:grid sm:grid-cols-1 md:grid-cols-2 sm:gap-6 sm:space-y-0">
                <div>
                    <x-input-label for="frequency" :value="__('Frequency')" />
                    <select id="frequency" name="frequency"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-base sm:text-sm"
                        required>
                        <option value="">Select frequency</option>
                        <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                    <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                </div>

                <x-input-with-label label="Start Date" name="start_date" type="date" min="{{ date('Y-m-d') }}"
                    value="{{ old('start_date', date('Y-m-d')) }}" required />
            </div>

            <div>
                <x-input-with-label label="End Date (Optional)" name="end_date" type="date"
                    min="{{ date('Y-m-d') }}" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description (Optional)')" />
                <textarea id="description" name="description" rows="3"
                    placeholder="Add any additional details about this recurring debt..."
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-base sm:text-sm">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="members" :value="__('Split Between')" />
                <div class="mt-3 space-y-3 sm:space-y-2">
                    @foreach ($group->users as $user)
                        <div class="relative flex items-start py-2 sm:py-1">
                            <div class="flex items-center h-6 sm:h-5">
                                <input
                                    class="form-checkbox h-5 w-5 sm:h-4 sm:w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2"
                                    type="checkbox" name="members[]" value="{{ $user->id }}"
                                    {{ in_array($user->id, old('members', [])) || old('members') === null ? 'checked' : '' }}
                                    id="user-{{ $user->id }}">
                            </div>
                            <div class="ml-3 text-base sm:text-sm">
                                <label for="user-{{ $user->id }}"
                                    class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                    {{ $user->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('members')" class="mt-2" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Select the members who will be included in this
                    recurring debt.</p>
            </div>

            <div
                class="flex flex-col-reverse space-y-reverse space-y-3 pt-6 border-t border-gray-200 dark:border-gray-700 sm:flex-row sm:space-y-0 sm:space-x-4 sm:justify-end">
                <a href="{{ route('groups.recurring-debts.index', $group) }}"
                    class="inline-flex items-center justify-center px-4 py-3 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-base sm:text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-3 sm:py-2 bg-indigo-600 border border-transparent rounded-lg text-base sm:text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Recurring Debt
                </button>
            </div>
        </form>

        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">About Recurring Debts</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Recurring debts automatically create new shared debts based on your selected frequency.
                            Perfect for regular expenses like rent, utilities, or subscriptions. You can pause, resume,
                            or modify them at any time.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-form-container>
</x-app-layout>
