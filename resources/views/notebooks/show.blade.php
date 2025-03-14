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
                        <input type="checkbox" id="select-all-sources" @change="toggleAllSources" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="select-all-sources" class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ __('Select All') }}</label>
                        
                        <button id="delete-selected-sources" @click="deleteSelectedSources" class="ml-auto text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 flex items-center">
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
                                        <input type="checkbox" id="source-{{ $source->id }}" name="source-{{ $source->id }}" @change="toggleSource({{ $source->id }})" {{ $source->is_active ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
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
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    Extraction Failed
                                                </span>
                                            @elseif(!$websiteContent['content'])
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                    <svg class="mr-1 h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Extracting...
                                                </span>
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Extracted
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

                        <div x-show="open" class="flex gap-2">
                            @foreach(['study-guide' => 'Study Guide', 'briefing' => 'Briefing Doc', 'faq' => 'FAQ'] as $type => $label)
                                <button type="button" @click="createNoteFormat('{{ $type }}')" class="flex-1 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                                    {{ __($label) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="open" class="mt-4 flex items-center">
                        <input type="checkbox" id="select-all-notes" @change="toggleAllNotes" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="select-all-notes" class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ __('Select All') }}</label>
                        
                        <div class="ml-auto flex items-center space-x-2">
                            <button id="convert-selected-notes" @click="convertSelectedNotes" class="text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-400 flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14L5 9l5-5" />
                                </svg>
                                <span class="ml-1 text-sm">{{ __('Convert') }}</span>
                            </button>
                            
                            <button id="delete-selected-notes" @click="deleteSelectedNotes" class="text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400 flex items-center">
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

    @include('notebooks.modals')

    <!-- Custom Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        Delete
                    </button>
                    <button type="button" id="cancel-action" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

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
            const confirmationModal = document.getElementById('confirmation-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const confirmButton = document.getElementById('confirm-action');
            const cancelButton = document.getElementById('cancel-action');
            
            function showConfirmationModal(title, message, callback) {
                modalTitle.textContent = title;
                modalMessage.textContent = message;
                confirmationModal.classList.remove('hidden');
                
                // Store the callback for later use
                confirmButton.onclick = function() {
                    callback();
                    confirmationModal.classList.add('hidden');
                };
                
                cancelButton.onclick = function() {
                    confirmationModal.classList.add('hidden');
                };
            }
            
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
                    }
                );
            });
        });
    </script>
    @endpush
</x-app-layout>
