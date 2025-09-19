@props([
    'variant' => 'primary',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
    'fullWidth' => false,
])

@php
    $baseClasses =
        'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm rounded-lg min-h-[36px]',
        'default' => 'px-4 py-3 sm:py-2 text-base sm:text-sm rounded-lg min-h-[44px]',
        'lg' => 'px-6 py-4 text-lg rounded-xl min-h-[52px]',
    ];

    $variantClasses = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-white border border-transparent',
        'secondary' =>
            'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-indigo-500 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border border-transparent',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white border border-transparent',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white border border-transparent',
        'info' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border border-transparent',
        'indigo' =>
            'bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/40 focus:ring-indigo-500 text-indigo-700 dark:text-indigo-400 border border-transparent',
        'yellow' =>
            'bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/40 focus:ring-yellow-500 text-yellow-700 dark:text-yellow-400 border border-transparent',
        'green' =>
            'bg-green-100 hover:bg-green-200 dark:bg-green-900/20 dark:hover:bg-green-900/40 focus:ring-green-500 text-green-700 dark:text-green-400 border border-transparent',
    ];

    $classes = collect([
        $baseClasses,
        $sizeClasses[$size] ?? $sizeClasses['default'],
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $fullWidth ? 'w-full' : '',
    ])
        ->filter()
        ->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
