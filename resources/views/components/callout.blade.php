@props([
    'type' => 'info',
    'title' => null,
    'icon' => null,
])

@php
    $typeClasses = [
        'info' => [
            'container' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
            'icon' => 'text-blue-400',
            'title' => 'text-blue-800 dark:text-blue-200',
            'content' => 'text-blue-800 dark:text-blue-200',
        ],
        'warning' => [
            'container' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800',
            'icon' => 'text-yellow-400',
            'title' => 'text-yellow-800 dark:text-yellow-200',
            'content' => 'text-yellow-800 dark:text-yellow-200',
        ],
        'success' => [
            'container' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
            'icon' => 'text-green-400',
            'title' => 'text-green-800 dark:text-green-200',
            'content' => 'text-green-800 dark:text-green-200',
        ],
        'error' => [
            'container' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
            'icon' => 'text-red-400',
            'title' => 'text-red-800 dark:text-red-200',
            'content' => 'text-red-800 dark:text-red-200',
        ],
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['info'];
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-lg border {$classes['container']}"]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            @if ($type === 'warning')
                <svg class="h-5 w-5 {{ $classes['icon'] }}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
            @elseif ($type === 'success')
                <svg class="h-5 w-5 {{ $classes['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif ($type === 'error')
                <svg class="h-5 w-5 {{ $classes['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @else
                <svg class="h-5 w-5 {{ $classes['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>
        <div class="ml-3">
            @if ($title)
                <h3 class="text-base sm:text-sm font-medium {{ $classes['title'] }}">
                    {{ $title }}
                </h3>
            @endif
            <div class="{{ $title ? 'mt-2' : '' }} text-sm {{ $classes['content'] }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
