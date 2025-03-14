<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NoteFlow') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
    <div class="relative min-h-screen">
        <!-- Background decorative elements -->
        <div class="fixed inset-0 -z-10">
            <div class="absolute inset-0">
                <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[100rem] overflow-hidden">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-[10%] aspect-[1155/678] w-[64rem] rotate-[30deg] bg-gradient-to-tr from-blue-600 to-indigo-500 opacity-30 blur-3xl"></div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-[20%] aspect-[1155/678] w-[64rem] -rotate-[30deg] bg-gradient-to-tr from-blue-600 to-indigo-500 opacity-30 blur-3xl"></div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-lg bg-white/70 dark:bg-gray-900/70 border-b border-gray-200 dark:border-gray-700">
            <div class="mx-auto max-w-7xl px-6 py-4">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900 dark:text-white">
                        NoteFlow
                    </a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex min-h-screen pt-[64px] items-center justify-center">
            <div class="w-full sm:max-w-md px-6">
                <!-- Logo -->
                <div class="mb-8 text-center">
                    <a href="/" class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-500">
                        NoteFlow
                    </a>
                </div>

                <!-- Content -->
                <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20 shadow-lg">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <!-- Dark mode toggle script -->
    <script>
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</body>
</html>
