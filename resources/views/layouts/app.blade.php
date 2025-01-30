<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Splitify') }}</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased dark:bg-gray-900">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Notifications -->
        <div x-data="{ show: true, messageType: '', message: '' }" x-init="@if (session('success')) messageType = 'success';
                        message = '{{ session('success') }}';
                        setTimeout(() => show = false, 3000);
                    @elseif(session('error'))
                        messageType = 'error';
                        message = '{{ session('error') }}';
                        setTimeout(() => show = false, 5000);
                    @elseif(session('warning'))
                        messageType = 'warning';
                        message = '{{ session('warning') }}';
                        setTimeout(() => show = false, 4000);
                    @elseif(session('info'))
                        messageType = 'info';
                        message = '{{ session('info') }}';
                        setTimeout(() => show = false, 4000); @endif" x-show="show && message"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90" class="fixed top-4 right-4 z-50"
            style="display: none;">
            <div :class="{
                'bg-green-500': messageType === 'success',
                'bg-red-500': messageType === 'error',
                'bg-yellow-500': messageType === 'warning',
                'bg-blue-500': messageType === 'info',
                'text-white': ['success', 'error', 'warning', 'info'].includes(messageType)
            }"
                class="p-4 rounded-md shadow-lg flex items-center justify-between" role="alert">
                <span x-text="message" class="block sm:inline"></span>
                <button @click="show = false" class="ml-4 focus:outline-none">
                    <svg class="h-5 w-5 fill-current opacity-50 hover:opacity-75" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
