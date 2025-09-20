@props([
    'name',
    'title' => 'Confirm Action',
    'description' => 'Are you sure you want to perform this action?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger', // danger, warning, info
    'formId' => null,
    'items' => [],
])

@php
    $variants = [
        'danger' => [
            'icon' => 'text-red-600 dark:text-red-400',
            'iconBg' => 'bg-red-100 dark:bg-red-900/50',
            'confirmButton' => 'danger',
            'iconPath' =>
                'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        ],
        'warning' => [
            'icon' => 'text-yellow-600 dark:text-yellow-400',
            'iconBg' => 'bg-yellow-100 dark:bg-yellow-900/50',
            'confirmButton' => 'primary',
            'iconPath' =>
                'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        ],
        'info' => [
            'icon' => 'text-blue-600 dark:text-blue-400',
            'iconBg' => 'bg-blue-100 dark:bg-blue-900/50',
            'confirmButton' => 'primary',
            'iconPath' =>
                'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        ],
    ];

    $currentVariant = $variants[$variant] ?? $variants['danger'];

    // Generate the JavaScript function for the confirm action
    $confirmAction = $formId
        ? "document.getElementById('{$formId}').submit(); \$dispatch('close-modal', '{$name}')"
        : "\$dispatch('close-modal', '{$name}')";
@endphp

<x-modal :name="$name" :show="false" max-width="lg" focusable>
    <div class="px-6 py-6">
        <div class="flex items-start">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $currentVariant['iconBg'] }}">
                    <svg class="w-6 h-6 {{ $currentVariant['icon'] }}" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $currentVariant['iconPath'] }}" />
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h3>

                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <p class="mb-3">{{ $description }}</p>

                    @if (count($items) > 0)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-3 mt-3">
                            <p class="font-medium text-gray-900 dark:text-gray-100 mb-2">This will permanently delete:
                            </p>
                            <ul class="space-y-1">
                                @foreach ($items as $item)
                                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="mt-3 font-medium text-gray-900 dark:text-gray-100">
                        This action cannot be undone.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <x-enhanced-button variant="secondary" type="button"
            x-on:click="$dispatch('close-modal', '{{ $name }}')">
            {{ $cancelText }}
        </x-enhanced-button>

        <x-enhanced-button :variant="$currentVariant['confirmButton']" type="button" x-on:click="{{ $confirmAction }}">
            {{ $confirmText }}
        </x-enhanced-button>
    </div>
</x-modal>
