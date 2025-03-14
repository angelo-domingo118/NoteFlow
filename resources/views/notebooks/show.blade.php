<x-app-layout>
    @include('layouts.navigation')
    
    <div class="fixed inset-0 top-16 bg-gray-50 dark:bg-gray-900" data-notebook-id="{{ $notebook->id }}">
        <div class="flex h-full">
            <!-- Sources Panel -->
            <div x-data="{ open: true }" :class="[open ? 'w-72' : 'w-16', 'transition-all duration-300']" class="relative z-30 flex flex-col bg-white dark:bg-gray-800 border-r dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between h-14 px-4 border-b dark:border-gray-700">
                    <h3 x-show="open" 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 -translate-x-2"
                       x-transition:enter-end="opacity-100 translate-x-0"
                       x-transition:leave="transition ease-in duration-150"
                       x-transition:leave-start="opacity-100 translate-x-0"
                       x-transition:leave-end="opacity-0 -translate-x-2"
                       class="font-semibold text-gray-900 dark:text-gray-100">Sources</h3>
                    <button @click="open = !open" class="flex-shrink-0 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                        <svg x-show="!open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-hidden flex flex-col p-4">
                    <!-- Add source button -->
                    <button type="button" @click="$dispatch('open-modal', 'add-source')" class="w-full">
                        <span x-show="open" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            {{ __('Add source') }}
                        </span>
                        <span x-show="!open" class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 hover:bg-blue-700">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                    </button>

                    <div x-show="open" class="mt-4 flex items-center">
                        <input type="checkbox" id="select-all-sources" @change="toggleAllSources" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="select-all-sources" class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ __('Select All') }}</label>
                    </div>
                    
                    <div class="mt-4 flex-1 overflow-y-auto sources-list scrollbar-minimal">
                        <div class="space-y-2">
                            @foreach($sources as $source)
                                <!-- Expanded view -->
                                <div x-show="open" class="flex items-center justify-between p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center overflow-hidden">
                                        <input type="checkbox" id="source-{{ $source->id }}" name="source-{{ $source->id }}" @change="toggleSource({{ $source->id }})" {{ $source->is_active ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="source-{{ $source->id }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100 truncate">{{ $source->name }}</label>
                                    </div>
                                    <div class="relative flex-shrink-0" x-data="{ menuOpen: false }">
                                        <button @click="menuOpen = !menuOpen" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>
                                        <div x-show="menuOpen" x-cloak @click.away="menuOpen = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5">
                                            <div class="py-1">
                                                <button @click="$dispatch('open-modal', 'rename-source-{{ $source->id }}')" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    {{ __('Rename') }}
                                                </button>
                                                <form method="POST" action="{{ route('sources.destroy', $source) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600" onclick="return confirm('Are you sure you want to delete this source?')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Collapsed view -->
                                <div x-show="!open" class="flex justify-center">
                                    <button @click="$dispatch('open-modal', 'rename-source-{{ $source->id }}')" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="{{ $source->name }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Panel -->
            <div class="flex-1 flex flex-col bg-white dark:bg-gray-800 min-w-0 relative z-20" id="chat-panel">
                <div class="flex items-center justify-between h-14 px-4 border-b dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $notebook->title }}
                        </h2>
                        <button type="button" id="edit-notebook-btn" class="ml-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                    <button id="refresh-chat-btn" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" title="Clear chat history">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-hidden chat-messages relative">
                    <div id="chat-messages" class="absolute inset-0 space-y-6 p-4 overflow-y-auto scrollbar-minimal">
                        <!-- Empty state with chat context -->
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="text-lg font-medium mb-2">Ask questions about your sources</h3>
                            <p class="max-w-md mx-auto">
                                Use this chat to ask questions about the content in your sources. The AI will analyze your active sources and provide relevant answers.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Chat loading indicator -->
                <div id="chat-loading" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 flex items-center space-x-3">
                        <div class="w-6 h-6 border-4 border-t-blue-600 dark:border-t-blue-500 border-gray-200 dark:border-gray-700 rounded-full animate-spin"></div>
                        <p class="text-gray-900 dark:text-gray-100">{{ __('AI is thinking...') }}</p>
                    </div>
                </div>

                <div class="p-1">
                    <form id="chat-form" autocomplete="off" class="bg-gray-50/60 dark:bg-gray-700/20 rounded-lg border border-gray-200/50 dark:border-gray-600/20">
                        @csrf
                        <div class="relative">
                            <label for="question" class="sr-only">{{ __('Your question') }}</label>
                            <textarea 
                                id="question"
                                name="question"
                                rows="1"
                                class="block w-full rounded-lg bg-transparent border-0 dark:text-gray-300 shadow-none focus:ring-0 sm:text-sm resize-none scrollbar-minimal pr-12 py-2 px-3"
                                placeholder="{{ __('Ask a question...') }}"
                            ></textarea>
                            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex items-center w-8 h-8 justify-center border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="loading-hide">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <svg class="loading-show hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notes Panel -->
            <div x-data="{ open: true }" :class="[open ? 'w-96' : 'w-16', 'transition-all duration-300']" class="relative z-30 flex flex-col bg-white dark:bg-gray-800 border-l dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between h-14 px-4 border-b dark:border-gray-700">
                    <h3 x-show="open" 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 -translate-x-2"
                       x-transition:enter-end="opacity-100 translate-x-0"
                       x-transition:leave="transition ease-in duration-150"
                       x-transition:leave-start="opacity-100 translate-x-0"
                       x-transition:leave-end="opacity-0 -translate-x-2"
                       class="font-semibold text-gray-900 dark:text-gray-100">Notes</h3>
                    <button @click="open = !open" class="flex-shrink-0 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                        <svg x-show="!open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-hidden flex flex-col p-4">
                    <div class="space-y-2">
                        <!-- New note button -->
                        <button type="button" @click="$dispatch('open-modal', 'add-note')" class="w-full">
                            <span x-show="open" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                {{ __('New Note') }}
                            </span>
                            <span x-show="!open" class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 hover:bg-blue-700">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" class="flex gap-2">
                            @foreach(['study-guide' => 'Study Guide', 'briefing' => 'Briefing Doc', 'faq' => 'FAQ'] as $type => $label)
                                <button type="button" @click="createNoteFormat('{{ $type }}')" class="flex-1 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                                    {{ __($label) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 flex-1 overflow-y-auto notes-list scrollbar-minimal">
                        <div class="space-y-4">
                            @foreach($notes as $note)
                                <!-- Expanded view -->
                                <div x-show="open" class="p-4 rounded-md bg-gray-50 dark:bg-gray-700">
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate flex-1 mr-4">{{ $note->title }}</h4>
                                        <div class="relative flex-shrink-0" x-data="{ menuOpen: false }">
                                            <button @click="menuOpen = !menuOpen" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="menuOpen" x-cloak @click.away="menuOpen = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5">
                                                <div class="py-1">
                                                    <button @click="$dispatch('open-modal', 'edit-note-{{ $note->id }}')" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        {{ __('Edit') }}
                                                    </button>
                                                    <form method="POST" action="{{ route('notes.convert', $note) }}">
                                                        @csrf
                                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            {{ __('Convert to Source') }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('notes.destroy', $note) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600" onclick="return confirm('Are you sure you want to delete this note?')">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($note->content, 200) }}</p>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $note->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                                <!-- Collapsed view -->
                                <div x-show="!open" class="flex justify-center">
                                    <button @click="$dispatch('open-modal', 'edit-note-{{ $note->id }}')" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="{{ $note->title }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('notebooks.modals')

    @push('scripts')
    <script>
        // All chat functionality is now handled by resources/js/chat.js
        // Additional UI functionality
        document.addEventListener('DOMContentLoaded', () => {
            // Layout adjustment function
            function adjustLayout() {
                const navHeight = document.querySelector('nav')?.offsetHeight || 0;
                const vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
                const container = document.querySelector('[data-notebook-id]');
                
                if (container) {
                    container.style.height = `${vh - navHeight}px`;
                    
                    document.querySelectorAll('.sources-list, .chat-messages, .notes-list').forEach(panel => {
                        const panelHeader = panel.closest('.flex-col')?.querySelector('.h-14');
                        const panelHeaderHeight = panelHeader?.offsetHeight || 0;
                        const panelFooter = panel.closest('.flex-col')?.querySelector('.border-t');
                        const panelFooterHeight = panelFooter?.offsetHeight || 0;
                        
                        const availableHeight = vh - navHeight - panelHeaderHeight - panelFooterHeight - 32;
                        panel.style.maxHeight = `${availableHeight}px`;
                    });
                }
            }

            // Initialize layout
            adjustLayout();
            new ResizeObserver(() => requestAnimationFrame(adjustLayout)).observe(document.body);
            window.addEventListener('resize', () => requestAnimationFrame(adjustLayout));

            // Note formats
            window.createNoteFormat = function(type) {
                const noteTypes = {
                    'study-guide': {
                        title: 'Study Guide',
                        content: '## Key Concepts\n\n## Summary\n\n## Important Points\n\n## Questions & Answers'
                    },
                    'briefing': {
                        title: 'Briefing Document',
                        content: '## Overview\n\n## Background\n\n## Key Points\n\n## Recommendations'
                    },
                    'faq': {
                        title: 'Frequently Asked Questions',
                        content: '## Questions\n\n1. \n\n2. \n\n3. \n\n## Additional Information'
                    }
                };

                const format = noteTypes[type];
                if (format) {
                    document.getElementById('title-new').value = format.title;
                    document.getElementById('content-new').value = format.content;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-note' }));
                }
            };
        });
    </script>
    @endpush
</x-app-layout>
