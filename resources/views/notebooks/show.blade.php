<x-app-layout>
    <style>
        /* Custom styles for the navigation in show.blade.php */
        .notebook-page nav {
            width: 100vw !important;
            max-width: 100% !important;
        }
        
        @media screen and (max-width: 100%) {
            .notebook-page nav {
                width: 100% !important;
            }
        }
        
        /* Ensure content adjusts properly with zoom */
        .notebook-page .flex-container {
            width: 100%;
            display: flex;
        }
        
        /* Toggle switch styles */
        #thinking-mode:checked ~ .dot {
            transform: translateX(100%);
            background-color: #ffffff;
        }
        
        #thinking-mode:checked ~ .block {
            background-color: #3b82f6;
        }
        
        .dot {
            transition: transform 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }
        
        /* Thinking mode animation */
        @keyframes thinking {
            0% { opacity: 0.4; }
            50% { opacity: 1; }
            100% { opacity: 0.4; }
        }
        
        #thinking-mode:checked ~ .dot {
            animation: thinking 1.5s infinite;
        }
    </style>
    
    <div class="notebook-page">
        @include('layouts.navigation')
        
        <div class="fixed inset-0 top-16 bg-gray-50 dark:bg-gray-900" data-notebook-id="{{ $notebook->id }}">
            <div class="flex h-full flex-container">
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
                           class="font-semibold text-gray-900 dark:text-gray-100">
                           <span class="flex items-center">
                               <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                               </svg>
                               Sources
                           </span>
                        </h3>
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
                            <input type="checkbox" id="select-all-sources" onchange="toggleAllSources(event)" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="select-all-sources" class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ __('Select All') }}</label>
                            
                            <button id="delete-selected-sources" onclick="deleteSelectedSources()" class="ml-auto text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span class="ml-1 text-sm">{{ __('Delete') }}</span>
                            </button>
                        </div>
                        
                        <div class="mt-4 flex-1 overflow-y-auto sources-list scrollbar-minimal">
                            <div class="space-y-2">
                                @foreach($sources as $source)
                                    <!-- Expanded view -->
                                    <div x-show="open" class="flex items-center justify-between p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center overflow-hidden">
                                            <input type="checkbox" id="source-{{ $source->id }}" name="source-{{ $source->id }}" onchange="toggleSource({{ $source->id }})" {{ $source->is_active ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <div class="ml-2 flex items-center">
                                                @if($source->isText())
                                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                @elseif($source->isWebsite())
                                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                    </svg>
                                                @elseif($source->isYouTube())
                                                    <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                                                    </svg>
                                                @elseif($source->isFile())
                                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                                <label for="source-{{ $source->id }}" class="block text-sm text-gray-900 dark:text-gray-100 truncate">{{ $source->name }}</label>
                                            </div>
                                            @if($source->isWebsite())
                                                @php
                                                    $websiteContent = $source->getWebsiteContent();
                                                @endphp
                                                @if($source->hasExtractionError())
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        <svg class="mr-1 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Error') }}
                                                    </span>
                                                @elseif(!$websiteContent['content'])
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        <svg class="mr-1 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Processing') }}
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <svg class="mr-1 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Ready') }}
                                                    </span>
                                                @endif
                                            @elseif($source->isYouTube())
                                                @php
                                                    $youtubeContent = $source->getYouTubeContent();
                                                @endphp
                                                @if($source->hasExtractionError())
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        <svg class="mr-1 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Error') }}
                                                    </span>
                                                @elseif(empty($youtubeContent['transcript']) && empty($youtubeContent['plain_text']) && !isset($youtubeContent['error']))
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        <svg class="mr-1 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Processing') }}
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <svg class="mr-1 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Ready') }}
                                                    </span>
                                                @endif
                                            @elseif($source->isFile() && $source->file_type === 'application/pdf')
                                                @php
                                                    $pdfContent = $source->getPdfContent();
                                                @endphp
                                                @if(isset($pdfContent['error']))
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        <svg class="mr-1 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Error') }}
                                                    </span>
                                                @elseif($pdfContent['processing'] || empty($pdfContent['content']))
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        <svg class="mr-1 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Processing') }}
                                                    </span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <svg class="mr-1 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{ __('Ready') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="relative flex-shrink-0" x-data="{ menuOpen: false }">
                                            <button @click="menuOpen = !menuOpen" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="menuOpen" x-cloak @click.away="menuOpen = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5">
                                                <div class="py-1">
                                                    <button @click="$dispatch('open-modal', 'edit-source-{{ $source->id }}')" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        {{ __('Edit') }}
                                                    </button>
                                                    <button @click="menuOpen = false; $dispatch('delete-source', {id: {{ $source->id }}, url: '{{ route('sources.destroy', $source) }}'})" class="block w-full px-4 py-2 text-left text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Collapsed view -->
                                    <div x-show="!open" class="flex justify-center">
                                        <button @click="$dispatch('open-modal', 'edit-source-{{ $source->id }}')" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="{{ $source->name }}">
                                            @if($source->isText())
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            @elseif($source->isWebsite())
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                </svg>
                                            @elseif($source->isYouTube())
                                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                                                </svg>
                                            @elseif($source->isFile())
                                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            @endif
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
                        <div class="flex items-center space-x-2">
                            <span id="active-sources-indicator" class="text-xs px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 flex items-center cursor-help" title="Number of active sources that will be used for AI responses">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span id="active-sources-count">0</span>&nbsp;<span class="hidden md:inline">sources active</span>
                            </span>
                            <button id="refresh-chat-btn" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 flex items-center" title="Clear chat history">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-hidden chat-messages relative">
                        <div id="chat-messages" class="absolute inset-0 p-4 overflow-y-auto scrollbar-minimal">
                            <!-- Empty state with chat context -->
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <h3 class="text-lg font-medium mb-2">Ask questions about your sources</h3>
                                <p class="max-w-md mx-auto">
                                    Use this chat to ask questions about the content in your sources. The AI will analyze your active sources and provide relevant answers.
                                </p>
                                
                                <!-- Quick start suggestions -->
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium mb-3">Try asking:</h4>
                                    <div id="suggestions" class="flex flex-col space-y-2 max-w-md mx-auto">
                                        <div id="loading-suggestions" class="hidden">
                                            <div class="flex justify-center items-center space-x-2 py-4">
                                                <div class="spinner-border animate-spin h-5 w-5 border-2 border-t-transparent border-blue-500 rounded-full"></div>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Generating suggestions...</span>
                                            </div>
                                        </div>
                                        
                                        <div id="default-suggestions">
                                            <button type="button" class="w-full text-sm px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                Summarize the key points from all sources
                                            </button>
                                            <button type="button" class="w-full text-sm px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                What are the main arguments presented?
                                            </button>
                                            <button type="button" class="w-full text-sm px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                Compare and contrast the different perspectives
                                            </button>
                                        </div>
                                        
                                        <div id="ai-suggestions" class="hidden flex flex-col space-y-2 w-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chat loading indicator -->
                        <div id="chat-loading" class="hidden absolute inset-0 bg-white/90 dark:bg-gray-800/90 flex items-center justify-center z-10">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center space-x-4">
                                    <div class="ai-thinking-animation">
                                        <div class="ai-thinking-bubble"></div>
                                        <div class="ai-thinking-bubble"></div>
                                        <div class="ai-thinking-bubble"></div>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-gray-900 dark:text-gray-100 font-medium">Generating response</p>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">Analyzing your sources...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Scroll to bottom button -->
                        <button id="scroll-to-bottom" class="hidden absolute bottom-16 right-4 p-2 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-auto border-t dark:border-gray-700">
                        <!-- Thinking mode toggle -->
                        <div class="px-3 pt-3 flex items-center justify-between">
                            <div class="flex items-center">
                                <label for="thinking-mode" class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" id="thinking-mode" class="sr-only" onchange="toggleThinkingMode(this.checked)">
                                        <div class="block bg-gray-200 dark:bg-gray-700 w-10 h-6 rounded-full"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                                    </div>
                                    <div class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                        Thinking Mode
                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Experimental
                                        </span>
                                        <span class="ml-1 cursor-help group relative">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                                                Thinking Mode uses an experimental AI model that shows its reasoning process. This helps you understand how the AI arrives at its answers by revealing its step-by-step thinking.
                                            </div>
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <!-- Source reference indicator - moved to right side -->
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Responses are based on your active sources</span>
                            </div>
                        </div>
                        
                        <form id="chat-form" autocomplete="off" class="bg-gray-800 dark:bg-gray-900 rounded-lg shadow-sm border border-gray-700 dark:border-gray-800 hover:border-blue-700 dark:hover:border-blue-800 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition-all m-3 mb-0">
                            @csrf
                            <div class="relative">
                                <div class="absolute left-3 top-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <label for="question" class="sr-only">{{ __('Your question') }}</label>
                                <textarea 
                                    id="question"
                                    name="question"
                                    rows="1"
                                    class="block w-full rounded-lg bg-transparent border-0 text-white shadow-none focus:ring-0 text-sm resize-none scrollbar-minimal pl-10 pr-10 py-3"
                                    placeholder="{{ __('Ask a question about your sources...') }}"
                                    style="min-height: 46px; height: 46px; overflow-y: hidden;"
                                ></textarea>
                            </div>
                            <div class="px-3 py-2 border-t border-gray-700 dark:border-gray-800 flex justify-between items-center text-xs text-gray-400">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Press Enter to send, Shift+Enter for new line</span>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                                    <span class="loading-hide">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7 7 7-7" transform="rotate(-90 12 12)" />
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
                           class="font-semibold text-gray-900 dark:text-gray-100">
                           <span class="flex items-center">
                               <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                               </svg>
                               Notes
                           </span>
                        </h3>
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

                        </div>

                        <div x-show="open" class="mt-4 flex items-center">
                            <input type="checkbox" id="select-all-notes" onchange="toggleAllNotes(event)" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="select-all-notes" class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ __('Select All') }}</label>
                            
                            <div class="ml-auto flex items-center space-x-2">
                                <button id="convert-selected-notes" class="text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-400 flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14L5 9l5-5" />
                                    </svg>
                                    <span class="ml-1 text-sm">{{ __('Convert') }}</span>
                                </button>
                                
                                <button id="delete-selected-notes" class="text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="ml-1 text-sm">{{ __('Delete') }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 flex-1 overflow-y-auto notes-list scrollbar-minimal">
                            <div class="space-y-4">
                                @foreach($notes as $note)
                                    <!-- Expanded view -->
                                    <div x-show="open" class="p-4 rounded-md bg-gray-50 dark:bg-gray-700">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="note-{{ $note->id }}" name="note-{{ $note->id }}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <h4 class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100 truncate flex-1 mr-4">{{ $note->title }}</h4>
                                            </div>
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
                                                        <button @click="menuOpen = false; $dispatch('delete-note', {id: {{ $note->id }}, url: '{{ route('notes.destroy', $note) }}'})" class="block w-full px-4 py-2 text-left text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            {{ __('Delete') }}
                                                        </button>
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
                                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
    </div>

    @include('notebooks.modals')

    <!-- Custom Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500/30 dark:bg-gray-800/30 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10" id="modal-icon-container">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="modal-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirm-action" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button type="button" id="cancel-action" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @push('scripts')
    <script>
        // All chat functionality is now handled by resources/js/chat.js
        // Additional UI functionality
        document.addEventListener('DOMContentLoaded', () => {
            // Declare questionInput once at the top so it can be used throughout
            const questionInput = document.getElementById('question');
            const characterCount = document.getElementById('character-count');
            
            // Auto-resize textarea based on content
            if (questionInput) {
                questionInput.addEventListener('input', function() {
                    // Reset height to auto to get the correct scrollHeight
                    this.style.height = 'auto';
                    // Set the height to match content (with a minimum height)
                    this.style.height = Math.max(46, this.scrollHeight) + 'px';
                });
                
                // Handle Enter key for submission and Shift+Enter for new line
                questionInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        const form = this.closest('form');
                        if (form && this.value.trim()) {
                            form.dispatchEvent(new Event('submit', { cancelable: true }));
                        }
                    }
                });
            }
            
            // Update source indicator based on count
            function updateSourceIndicator() {
                const countElement = document.getElementById('active-sources-count');
                const indicator = document.getElementById('active-sources-indicator');
                
                if (countElement && indicator) {
                    const count = parseInt(countElement.textContent || '0');
                    
                    if (count === 0) {
                        // Use gray colors for zero sources
                        indicator.classList.remove('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-600', 'dark:text-blue-400');
                        indicator.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
                        
                        // Show default suggestions when no sources are active
                        document.getElementById('default-suggestions').classList.remove('hidden');
                        document.getElementById('ai-suggestions').classList.add('hidden');
                    } else {
                        // Use blue colors for active sources
                        indicator.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
                        indicator.classList.add('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-600', 'dark:text-blue-400');
                        
                        // Generate AI suggestions when sources are active
                        generateAiSuggestions();
                    }
                }
            }
            
            // Call initially and observe for changes
            updateSourceIndicator();
            
            // Set up a mutation observer to detect changes in the source count
            const countElement = document.getElementById('active-sources-count');
            if (countElement) {
                const observer = new MutationObserver(updateSourceIndicator);
                observer.observe(countElement, { childList: true, characterData: true, subtree: true });
            }
            
            // Function to generate AI suggestions based on active sources
            function generateAiSuggestions() {
                const activeSources = Array.from(document.querySelectorAll('input[name^="source-"]:checked')).map(
                    checkbox => ({
                        id: checkbox.id.replace('source-', ''),
                        name: checkbox.nextElementSibling?.querySelector('label')?.textContent || 'Source'
                    })
                );
                
                if (activeSources.length === 0) return;
                
                // Show loading state
                document.getElementById('default-suggestions').classList.add('hidden');
                document.getElementById('loading-suggestions').classList.remove('hidden');
                document.getElementById('ai-suggestions').classList.add('hidden');
                
                const notebookId = document.querySelector('[data-notebook-id]').dataset.notebookId;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                // Call the API to generate suggestions
                fetch(`/api/notebooks/${notebookId}/generate-suggestions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        sources: activeSources.map(s => s.id),
                        model: 'gemini-2.0-flash-lite'
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to generate suggestions');
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading state
                    document.getElementById('loading-suggestions').classList.add('hidden');
                    
                    if (data.suggestions && data.suggestions.length > 0) {
                        // Display AI-generated suggestions
                        const aiSuggestionsContainer = document.getElementById('ai-suggestions');
                        aiSuggestionsContainer.innerHTML = '';
                        
                        data.suggestions.forEach(suggestion => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'w-full text-sm px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors';
                            button.textContent = suggestion;
                            button.addEventListener('click', () => {
                                // Fill the chat input with this suggestion
                                if (questionInput) {
                                    questionInput.value = suggestion;
                                    questionInput.focus();
                                }
                            });
                            
                            aiSuggestionsContainer.appendChild(button);
                        });
                        
                        aiSuggestionsContainer.classList.remove('hidden');
                    } else {
                        // Show default suggestions if no AI suggestions were generated
                        document.getElementById('default-suggestions').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error generating suggestions:', error);
                    // Show default suggestions on error
                    document.getElementById('loading-suggestions').classList.add('hidden');
                    document.getElementById('default-suggestions').classList.remove('hidden');
                });
            }
            
            // Add click handlers for default suggestions
            document.querySelectorAll('#default-suggestions button').forEach(button => {
                button.addEventListener('click', () => {
                    if (questionInput) {
                        questionInput.value = button.textContent.trim();
                        questionInput.focus();
                    }
                });
            });
            
            // Layout adjustment function
            function adjustLayout() {
                const navHeight = document.querySelector('nav')?.offsetHeight || 0;
                const vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
                const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
                const container = document.querySelector('[data-notebook-id]');
                
                // Ensure navigation bar spans full width in notebook page
                const notebookPage = document.querySelector('.notebook-page');
                if (notebookPage) {
                    const nav = notebookPage.querySelector('nav');
                    if (nav) {
                        nav.style.width = '100vw';
                        
                        // Ensure the right-side elements stay at the end
                        const rightElements = nav.querySelector('.flex.justify-between');
                        if (rightElements) {
                            rightElements.style.width = '100%';
                        }
                    }
                }
                
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
            // Also adjust on zoom (for browsers that support it)
            window.addEventListener('zoom', () => requestAnimationFrame(adjustLayout));
            // Fallback for browsers that don't support zoom event
            window.visualViewport?.addEventListener('resize', () => requestAnimationFrame(adjustLayout));
            // Additional fallback for zoom
            document.addEventListener('wheel', (e) => {
                if (e.ctrlKey) {
                    // This is likely a zoom gesture
                    requestAnimationFrame(adjustLayout);
                }
            });

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
            
            // YouTube URL validation
            const sourceUrlInput = document.getElementById('source_url');
            const sourceType = document.querySelector('input[name="type"][value="link"]');
            
            if (sourceUrlInput && sourceType) {
                sourceType.addEventListener('change', function() {
                    // Set focus on URL input when YouTube option is selected
                    if (this.checked) {
                        setTimeout(() => sourceUrlInput.focus(), 100);
                    }
                });
                
                sourceUrlInput.addEventListener('input', function() {
                    const url = this.value.trim();
                    const isValidUrl = /^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/.test(url);
                    
                    if (url && !isValidUrl) {
                        this.setCustomValidity('Please enter a valid YouTube URL');
                    } else {
                        this.setCustomValidity('');
                        
                        // Auto-fill name field with video title if empty
                        if (isValidUrl) {
                            const nameInput = document.getElementById('source_name');
                            if (nameInput && !nameInput.value.trim()) {
                                const videoId = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/)[1];
                                fetchYouTubeTitle(videoId, nameInput);
                            }
                        }
                    }
                });
                
                // Function to fetch YouTube video title
                function fetchYouTubeTitle(videoId, nameInput) {
                    const apiUrl = `https://noembed.com/embed?url=https://www.youtube.com/watch?v=${videoId}&format=json`;
                    
                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.title) {
                                nameInput.value = data.title;
                            }
                        })
                        .catch(error => console.error('Error fetching video title:', error));
                }
            }
            
            // Source toggling
            window.toggleSource = function(sourceId) {
                const checkbox = document.getElementById(`source-${sourceId}`);
                if (checkbox) {
                    fetch(`/sources/${sourceId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .catch(error => {
                        console.error('Error toggling source:', error);
                        // Revert checkbox state on error
                        checkbox.checked = !checkbox.checked;
                    });
                }
            };
            
            // Select all sources
            window.toggleAllSources = function(event) {
                const isChecked = event.target.checked;
                document.querySelectorAll('input[name^="source-"]').forEach(checkbox => {
                    if (checkbox.checked !== isChecked) {
                        checkbox.checked = isChecked;
                        const sourceId = checkbox.id.replace('source-', '');
                        window.toggleSource(sourceId);
                    }
                });
            };
            
            // Custom confirmation modal functionality
            window.showConfirmationModal = function(title, message, callback, actionType = 'delete') {
                const confirmationModal = document.getElementById('confirmation-modal');
                const modalTitle = document.getElementById('modal-title');
                const modalMessage = document.getElementById('modal-message');
                const confirmButton = document.getElementById('confirm-action');
                const cancelButton = document.getElementById('cancel-action');
                const modalIconContainer = document.getElementById('modal-icon-container');
                const modalIcon = document.getElementById('modal-icon');
                
                modalTitle.textContent = title;
                modalMessage.textContent = message;
                confirmationModal.classList.remove('hidden');
                
                // Set button text and styles based on action type
                if (actionType === 'convert') {
                    confirmButton.textContent = 'Convert';
                    confirmButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
                    confirmButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
                    
                    modalIconContainer.classList.remove('bg-red-100', 'dark:bg-red-900');
                    modalIconContainer.classList.add('bg-blue-100', 'dark:bg-blue-900');
                    
                    modalIcon.classList.remove('text-red-600', 'dark:text-red-400');
                    modalIcon.classList.add('text-blue-600', 'dark:text-blue-400');
                    
                    // Change icon to convert icon
                    modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14L5 9l5-5" />';
                } else {
                    confirmButton.textContent = 'Delete';
                    confirmButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
                    confirmButton.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
                    
                    modalIconContainer.classList.remove('bg-blue-100', 'dark:bg-blue-900');
                    modalIconContainer.classList.add('bg-red-100', 'dark:bg-red-900');
                    
                    modalIcon.classList.remove('text-blue-600', 'dark:text-blue-400');
                    modalIcon.classList.add('text-red-600', 'dark:text-red-400');
                    
                    // Change icon to warning icon
                    modalIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                }
                
                // Store the callback for later use
                confirmButton.onclick = function() {
                    callback();
                    confirmationModal.classList.add('hidden');
                };
                
                cancelButton.onclick = function() {
                    confirmationModal.classList.add('hidden');
                };
            };
            
            // Delete selected sources
            document.getElementById('delete-selected-sources').addEventListener('click', function() {
                const selectedSources = Array.from(document.querySelectorAll('input[name^="source-"]:checked')).map(
                    checkbox => checkbox.id.replace('source-', '')
                );
                
                if (selectedSources.length === 0) {
                    alert('Please select at least one source to delete.');
                    return;
                }
                
                showConfirmationModal(
                    'Delete Sources',
                    `Are you sure you want to delete ${selectedSources.length} selected source(s)?`,
                    function() {
                        const notebookId = document.querySelector('[data-notebook-id]').dataset.notebookId;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        
                        fetch(`/notebooks/${notebookId}/sources/batch-delete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ sources: selectedSources })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Failed to delete sources');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting sources:', error);
                            alert('Failed to delete sources. Please try again.');
                        });
                    }
                );
            });
            
            // Delete selected notes
            document.getElementById('delete-selected-notes').addEventListener('click', function() {
                const selectedNotes = Array.from(document.querySelectorAll('input[name^="note-"]:checked')).map(
                    checkbox => checkbox.id.replace('note-', '')
                );
                
                if (selectedNotes.length === 0) {
                    alert('Please select at least one note to delete.');
                    return;
                }
                
                showConfirmationModal(
                    'Delete Notes',
                    `Are you sure you want to delete ${selectedNotes.length} selected note(s)?`,
                    function() {
                        const notebookId = document.querySelector('[data-notebook-id]').dataset.notebookId;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        
                        fetch(`/notebooks/${notebookId}/notes/batch-delete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ notes: selectedNotes })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Failed to delete notes');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting notes:', error);
                            alert('Failed to delete notes. Please try again.');
                        });
                    }
                );
            });
            
            // Select all notes
            window.toggleAllNotes = function(event) {
                const isChecked = event.target.checked;
                document.querySelectorAll('input[name^="note-"]').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            };
            
            // Convert selected notes to sources
            document.getElementById('convert-selected-notes').addEventListener('click', function() {
                const selectedNotes = Array.from(document.querySelectorAll('input[name^="note-"]:checked')).map(
                    checkbox => checkbox.id.replace('note-', '')
                );
                
                if (selectedNotes.length === 0) {
                    alert('Please select at least one note to convert.');
                    return;
                }
                
                showConfirmationModal(
                    'Convert Notes to Sources',
                    `Are you sure you want to convert ${selectedNotes.length} selected note(s) to sources?`,
                    function() {
                        const notebookId = document.querySelector('[data-notebook-id]').dataset.notebookId;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        
                        fetch(`/notebooks/${notebookId}/notes/batch-convert`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ notes: selectedNotes })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Failed to convert notes');
                            }
                        })
                        .catch(error => {
                            console.error('Error converting notes:', error);
                            alert('Failed to convert notes. Please try again.');
                        });
                    },
                    'convert'
                );
            });
            
            // Define the deleteSelectedSources function
            window.deleteSelectedSources = function() {
                const selectedSources = Array.from(document.querySelectorAll('input[name^="source-"]:checked')).map(
                    checkbox => checkbox.id.replace('source-', '')
                );
                
                if (selectedSources.length === 0) {
                    alert('Please select at least one source to delete.');
                    return;
                }
                
                showConfirmationModal(
                    'Delete Sources',
                    `Are you sure you want to delete ${selectedSources.length} selected source(s)?`,
                    function() {
                        const notebookId = document.querySelector('[data-notebook-id]').dataset.notebookId;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        
                        fetch(`/notebooks/${notebookId}/sources/batch-delete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ sources: selectedSources })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Failed to delete sources');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting sources:', error);
                            alert('Failed to delete sources. Please try again.');
                        });
                    }
                );
            };

            // Thinking mode toggle functionality
            window.toggleThinkingMode = function(isEnabled) {
                // Store the preference in localStorage
                localStorage.setItem('thinking_mode_enabled', isEnabled ? 'true' : 'false');
                
                // Update the UI to reflect the current mode
                const toggle = document.getElementById('thinking-mode');
                if (toggle) {
                    toggle.checked = isEnabled;
                    
                    // The CSS will handle the animation and styling
                    // No need to manually update styles here
                }
                
                console.log('Thinking mode ' + (isEnabled ? 'enabled' : 'disabled'));
            };
            
            // Initialize thinking mode from localStorage
            const thinkingModeEnabled = localStorage.getItem('thinking_mode_enabled') === 'true';
            const thinkingModeToggle = document.getElementById('thinking-mode');
            if (thinkingModeToggle) {
                thinkingModeToggle.checked = thinkingModeEnabled;
                toggleThinkingMode(thinkingModeEnabled);
            }
            
            // Character count for chat input
            if (questionInput && characterCount) {
                questionInput.addEventListener('input', function() {
                    const count = this.value.length;
                    characterCount.textContent = count;
                    
                    // Optional: Add color indication for character limits
                    if (count > 2000) {
                        characterCount.classList.add('text-red-500');
                        characterCount.classList.remove('text-gray-400', 'text-yellow-500');
                    } else if (count > 1500) {
                        characterCount.classList.add('text-yellow-500');
                        characterCount.classList.remove('text-gray-400', 'text-red-500');
                    } else {
                        characterCount.classList.add('text-gray-400');
                        characterCount.classList.remove('text-yellow-500', 'text-red-500');
                    }
                });
            }
        });

        // Function to handle source deletion
        function deleteSource(sourceId, sourceUrl) {
            showConfirmationModal(
                '{{ __('Delete Source') }}',
                '{{ __('Are you sure you want to delete this source? This action cannot be undone.') }}',
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = sourceUrl;
                    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }
    
        // Additional UI functionality
        document.addEventListener('DOMContentLoaded', () => {
            // ... existing code ...
        });

        // Event listener for delete-source event
        window.addEventListener('delete-source', (event) => {
            const { id, url } = event.detail;
            showConfirmationModal(
                '{{ __('Delete Source') }}',
                '{{ __('Are you sure you want to delete this source? This action cannot be undone.') }}',
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        });

        // Event listener for delete-note event
        window.addEventListener('delete-note', (event) => {
            const { id, url } = event.detail;
            showConfirmationModal(
                '{{ __('Delete Note') }}',
                '{{ __('Are you sure you want to delete this note? This action cannot be undone.') }}',
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            );
        });
    </script>
    @endpush
</x-app-layout>
