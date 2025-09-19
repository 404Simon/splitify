@props([
    'headers' => [],
    'mobileHeaders' => [],
])

@php
    $mobileHeaders = !empty($mobileHeaders) ? $mobileHeaders : $headers;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ $header }}
                        </th>
                    @endforeach
                    @if (isset($actions))
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                {{ $desktop ?? $slot }}
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden">
        {{ $mobile ?? $slot }}
    </div>
</div>
