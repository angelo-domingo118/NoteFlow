<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NoteFlow') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 {{ !request()->routeIs('profile.edit') ? 'overflow-hidden' : '' }}">
    <div class="min-h-screen">
        @if(!request()->routeIs('notebooks.show'))
            @include('layouts.navigation')
        @endif
        
        <!-- Page Content -->
        <main class="pt-16">
            @if(!request()->routeIs('notebooks.show'))
                <header class="bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border-b border-gray-200/20 dark:border-gray-700/20 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header ?? '' }}
                    </div>
                </header>
            @endif

            {{ $slot }}
        </main>
    </div>
    @stack('modals')
    @stack('scripts')
</body>
</html>
