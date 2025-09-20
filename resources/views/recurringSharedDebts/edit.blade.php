<x-app-layout>
    <x-form-container title="Edit Recurring Debt: {{ $recurringDebt->name }}">
        <form action="{{ route('groups.recurring-debts.update', [$group, $recurringDebt]) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <x-input-with-label label="Name" name="name" type="text" :value="old('name', $recurringDebt->name)" required />
                <x-input-with-label label="Amount (€)" name="amount" type="number" step="0.01" min="0.01"
                    :value="old('amount', $recurringDebt->amount)" required />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <x-input-label for="frequency" :value="__('Frequency')" />
                    <select id="frequency" name="frequency"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-base sm:text-sm py-3 sm:py-2"
                        required>
                        <option value="">Select frequency</option>
                        <option value="daily"
                            {{ old('frequency', $recurringDebt->frequency) === 'daily' ? 'selected' : '' }}>Daily
                        </option>
                        <option value="weekly"
                            {{ old('frequency', $recurringDebt->frequency) === 'weekly' ? 'selected' : '' }}>Weekly
                        </option>
                        <option value="monthly"
                            {{ old('frequency', $recurringDebt->frequency) === 'monthly' ? 'selected' : '' }}>Monthly
                        </option>
                        <option value="yearly"
                            {{ old('frequency', $recurringDebt->frequency) === 'yearly' ? 'selected' : '' }}>Yearly
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                </div>

                <x-input-with-label label="End Date (Optional)" name="end_date" type="date" min="{{ date('Y-m-d') }}"
                    :value="old('end_date', $recurringDebt->end_date?->format('Y-m-d'))" />
            </div>

            <div>
                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                        {{ old('is_active', $recurringDebt->is_active) ? 'checked' : '' }}
                        class="h-5 w-5 sm:h-4 sm:w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2">
                    <label for="is_active"
                        class="ml-3 sm:ml-2 block text-base sm:text-sm text-gray-900 dark:text-gray-300 font-medium">
                        Active
                    </label>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">When checked, this recurring debt will
                    automatically generate new shared debts based on the schedule.</p>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description (Optional)')" />
                <textarea id="description" name="description" rows="3"
                    placeholder="Add any additional details about this recurring debt..."
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-base sm:text-sm py-3 sm:py-2">{{ old('description', $recurringDebt->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Mobile-optimized member selection -->
            <div>
                <x-input-label for="members" :value="__('Split Between')" />
                <div class="mt-3 space-y-4 sm:space-y-3">
                    @foreach ($group->users as $user)
                        <div class="relative flex items-start">
                            <div class="flex items-center h-6 sm:h-5">
                                <input
                                    class="form-checkbox h-5 w-5 sm:h-4 sm:w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2"
                                    type="checkbox" name="members[]" value="{{ $user->id }}"
                                    {{ in_array($user->id, old('members', $recurringDebt->users->pluck('id')->toArray())) ? 'checked' : '' }}
                                    id="user-{{ $user->id }}">
                            </div>
                            <div class="ml-3 text-base sm:text-sm">
                                <label for="user-{{ $user->id }}"
                                    class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer block py-1">
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
                class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-enhanced-button variant="secondary" :href="route('groups.recurring-debts.show', [$group, $recurringDebt])">
                    Cancel
                </x-enhanced-button>
                <x-enhanced-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Update Recurring Debt
                </x-enhanced-button>
            </div>
        </form>

        <x-callout type="warning" title="Important Note" class="mt-8">
            Changes to the frequency or amount will only affect future generated debts. Previously
            generated shared debts will remain unchanged.
        </x-callout>
    </x-form-container>
</x-app-layout>
