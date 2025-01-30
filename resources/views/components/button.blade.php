@props([
    'type' => 'submit',
    'variant' => 'primary', // Default variant is 'primary'
])

@php
    $variantClasses = [
        'primary' =>
            'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600',
        'secondary' => 'bg-gray-500 hover:bg-gray-600 focus:ring-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 dark:bg-red-500 dark:hover:bg-red-600',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 dark:bg-green-500 dark:hover:bg-green-600',
        'warning' =>
            'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400 dark:bg-yellow-600 dark:hover:bg-yellow-700',
    ];

    $defaultClasses =
        'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2';
    $classes = $defaultClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']); // Default to primary if variant is not found
@endphp

<button type="{{ $type }}" class="{{ $classes }}">
    {{ $slot }}
</button>
