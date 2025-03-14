<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'NoteFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-lg bg-white/70 dark:bg-gray-900/70 border-b border-gray-200 dark:border-gray-700">
        <div class="mx-auto max-w-7xl px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900 dark:text-white">
                        NoteFlow
                    </a>
                </div>
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none transition-colors duration-200">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none transition-colors duration-200">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600/90 backdrop-blur-sm border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <main class="relative min-h-screen overflow-hidden">
        <!-- Hero Section -->
        <div class="relative isolate px-6 lg:px-8">
            <!-- Background decorative elements -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-blue-600 to-indigo-500 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
            </div>

            <!-- Main content -->
            <div class="mx-auto max-w-4xl py-32 sm:py-48 lg:py-56">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl mb-8 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-500">
                        Organize Your Notes with AI-Powered Intelligence
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300 mb-12">
                        Transform your note-taking experience with NoteFlow. Powered by Gemini AI, our platform helps you create, organize, and explore your notes in ways you never thought possible.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="{{ route('register') }}" class="rounded-md bg-blue-600/90 backdrop-blur-sm px-6 py-3 text-lg font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200">
                            Start Taking Notes
                        </a>
                        <a href="{{ route('login') }}" class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Already have an account? <span class="text-blue-600 dark:text-blue-400">Log in</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Feature Highlights -->
            <div class="mx-auto max-w-7xl px-6 lg:px-8 pb-16">
                <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20 shadow-lg transition-all duration-300 hover:shadow-xl">
                        <h3 class="text-xl font-semibold leading-7 text-gray-900 dark:text-white">Smart AI Assistant</h3>
                        <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-400">
                            Powered by Gemini AI, get intelligent responses and insights based on your notes and sources.
                        </p>
                    </div>
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20 shadow-lg transition-all duration-300 hover:shadow-xl">
                        <h3 class="text-xl font-semibold leading-7 text-gray-900 dark:text-white">Organized Notebooks</h3>
                        <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-400">
                            Create notebooks to organize your notes, with support for rich text editing and file attachments.
                        </p>
                    </div>
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20 shadow-lg transition-all duration-300 hover:shadow-xl">
                        <h3 class="text-xl font-semibold leading-7 text-gray-900 dark:text-white">Smart Context</h3>
                        <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-400">
                            Convert notes into sources for enhanced AI context, enabling more accurate and relevant responses.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detailed Features -->
            <div class="mx-auto max-w-7xl px-6 lg:px-8 pb-32">
                <div class="mb-16 text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Powerful Features for Modern Note-Taking
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                        Everything you need to manage and enhance your notes with AI assistance.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- AI Chat Integration -->
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">AI-Powered Chat</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Engage in natural conversations with our AI assistant. Get instant answers, summaries, and insights based on your notes and sources.
                        </p>
                    </div>

                    <!-- Source Management -->
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Smart Source Management</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Import various types of sources including PDFs, links, and text. Convert your notes into sources for enhanced AI context and better responses.
                        </p>
                    </div>

                    <!-- Rich Text Editing -->
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Rich Text Editor</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Format your notes with a powerful rich text editor. Add headings, lists, links, and more to create well-structured documents.
                        </p>
                    </div>

                    <!-- Organization -->
                    <div class="p-8 rounded-2xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Effortless Organization</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Keep your notes organized in notebooks. Use the intuitive interface to manage, search, and access your content quickly.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional decorative elements -->
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-blue-600 to-indigo-500 opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"></div>
            </div>
        </div>
    </main>

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
