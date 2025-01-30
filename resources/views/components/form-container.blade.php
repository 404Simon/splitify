@props(['title'])

<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
            {{ $title }}
        </h1>
        {{ $slot }}
    </div>
</div>
