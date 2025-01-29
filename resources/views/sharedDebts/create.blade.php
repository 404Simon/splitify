<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <h1 class="text-xl font-bold text-gray-900 mb-6 dark:text-white"> {{-- Dark mode text --}}
                Add New Shared Debt to {{ $group->name }}
            </h1>
            <form action="{{ route('sharedDebts.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Debt
                        Name</label>
                    <input type="text" name="name" id="name"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white"
                        required>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount
                        (€)</label>
                    <input type="number" step="0.01" name="amount" id="amount"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white"
                        required>
                </div>

                <div>
                    <label for="members" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Split
                        Between</label>
                    <div class="mt-2 space-y-2">
                        @foreach ($group->users as $user)
                            <div class="flex items-center">
                                <input
                                    class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 dark:ring-offset-gray-800 focus:ring-2"
                                    {{-- Dark mode checkbox styles --}} type="checkbox" name="members[]" value="{{ $user->id }}"
                                    id="user-{{ $user->id }}">
                                <label class="ml-2 text-gray-700 dark:text-gray-300" for="user-{{ $user->id }}">
                                    {{ $user->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">Add
                    Debt</button>

            </form>
        </div>
    </div>
</x-app-layout>
