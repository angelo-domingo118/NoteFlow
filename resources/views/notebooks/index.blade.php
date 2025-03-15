<x-app-layout>
    <x-slot name="header">
        <!-- Replace Alpine store with traditional JavaScript for view mode -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get view mode from localStorage or default to grid
                const viewMode = localStorage.getItem('viewMode') || 'grid';
                
                // Initialize the view
                updateViewButtons(viewMode);
                updateView(viewMode);
                
                // Set up event listeners for view buttons
                document.getElementById('grid-view-btn').addEventListener('click', function() {
                    setViewMode('grid');
                });
                
                document.getElementById('list-view-btn').addEventListener('click', function() {
                    setViewMode('list');
                });
                
                // Function to update view mode
                function setViewMode(mode) {
                    localStorage.setItem('viewMode', mode);
                    updateViewButtons(mode);
                    updateView(mode);
                }
                
                // Update active state of view buttons
                function updateViewButtons(mode) {
                    const gridBtn = document.getElementById('grid-view-btn');
                    const listBtn = document.getElementById('list-view-btn');
                    
                    if (mode === 'grid') {
                        gridBtn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white');
                        gridBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
                        listBtn.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white');
                        listBtn.classList.add('text-gray-500', 'dark:text-gray-400');
                    } else {
                        listBtn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white');
                        listBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
                        gridBtn.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white');
                        gridBtn.classList.add('text-gray-500', 'dark:text-gray-400');
                    }
                }
                
                // Show appropriate view based on mode
                function updateView(mode) {
                    const gridView = document.getElementById('grid-view');
                    const listView = document.getElementById('list-view');
                    
                    if (mode === 'grid') {
                        gridView.style.display = 'grid';
                        listView.style.display = 'none';
                    } else {
                        gridView.style.display = 'none';
                        listView.style.display = 'block';
                    }
                }
                
                // Setup sort dropdown
                const sortDropdownBtn = document.getElementById('sort-dropdown-btn');
                const sortDropdown = document.getElementById('sort-dropdown');
                
                sortDropdownBtn.addEventListener('click', function() {
                    sortDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!sortDropdownBtn.contains(event.target) && !sortDropdown.contains(event.target)) {
                        sortDropdown.classList.add('hidden');
                    }
                });
                
                // Set up sorting buttons
                document.querySelectorAll('.sort-option').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const sortBy = this.getAttribute('data-sort');
                        document.getElementById('sort-by').value = sortBy;
                        document.getElementById('sort-form').submit();
                        sortDropdown.classList.add('hidden');
                    });
                });
            });
            
            // Function to toggle notebook menu
            function toggleMenu(id) {
                event.preventDefault();
                event.stopPropagation();
                
                const menu = document.getElementById('menu-' + id);
                menu.classList.toggle('hidden');
                
                // Close other open menus
                document.querySelectorAll('.notebook-menu').forEach(function(m) {
                    if (m.id !== 'menu-' + id && !m.classList.contains('hidden')) {
                        m.classList.add('hidden');
                    }
                });
                
                // Close menu when clicking elsewhere
                const handleClickAway = function(event) {
                    if (!menu.contains(event.target) && event.target.id !== 'menu-button-' + id) {
                        menu.classList.add('hidden');
                        document.removeEventListener('click', handleClickAway);
                    }
                };
                
                if (!menu.classList.contains('hidden')) {
                    // Add with slight delay to avoid immediate triggering
                    setTimeout(() => {
                        document.addEventListener('click', handleClickAway);
                    }, 10);
                }
            }
            
            // Function to open modal
            function openModal(id) {
                const modal = document.getElementById(id);
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                
                // Close button functionality
                const closeButtons = modal.querySelectorAll('[data-close-modal]');
                closeButtons.forEach(button => {
                    button.addEventListener('click', () => closeModal(id));
                });
                
                // Close on backdrop click
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal(id);
                    }
                });
                
                // Close on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        closeModal(id);
                    }
                });
            }
            
            // Function to close modal
            function closeModal(id) {
                const modal = document.getElementById(id);
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            }
        </script>
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Welcome to NoteFlow
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage and organize your notebooks
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Grid/List Toggle -->
                <div class="flex items-center rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <button id="grid-view-btn" type="button" class="p-2 hover:text-gray-900 dark:hover:text-white transition-colors rounded-l-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button id="list-view-btn" type="button" class="p-2 hover:text-gray-900 dark:hover:text-white transition-colors rounded-r-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Sort Dropdown with form submission -->
                <form id="sort-form" action="{{ route('notebooks.index') }}" method="GET" class="hidden">
                    <input type="hidden" id="sort-by" name="sort" value="{{ request('sort', 'recent') }}">
                </form>
                <div class="relative">
                    <button id="sort-dropdown-btn" type="button" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white bg-white/20 dark:bg-gray-800/40 backdrop-blur-lg border border-gray-200/20 dark:border-gray-500/20 rounded-lg shadow-sm transition-all duration-200 hover:shadow">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        <span id="current-sort">{{ request('sort') === 'alpha' ? 'Alphabetical' : (request('sort') === 'modified' ? 'Last Modified' : 'Most Recent') }}</span>
                    </button>
                    <div id="sort-dropdown" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg border border-gray-200/20 dark:border-gray-700/20 z-10">
                        <div class="py-1">
                            <button data-sort="recent" class="sort-option block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">Most Recent</button>
                            <button data-sort="alpha" class="sort-option block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">Alphabetical</button>
                            <button data-sort="modified" class="sort-option block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">Last Modified</button>
                        </div>
                    </div>
                </div>

                <!-- Create New Button -->
                <button type="button" onclick="openModal('create-notebook')" class="flex items-center px-4 py-2 bg-blue-600/90 backdrop-blur-sm border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm hover:shadow transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Notebook
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Empty State -->
            <div style="{{ count($notebooks) ? 'display: none;' : '' }}" class="text-center py-12 bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20 dark:border-gray-700/20 rounded-xl shadow-sm">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No notebooks yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first notebook.</p>
                <div class="mt-6">
                    <button type="button" onclick="openModal('create-notebook')" class="inline-flex items-center px-4 py-2 bg-blue-600/90 backdrop-blur-sm border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Notebook
                    </button>
                </div>
            </div>

            <!-- Grid View -->
            <div id="grid-view" style="{{ count($notebooks) ? '' : 'display: none;' }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($notebooks as $notebook)
                <div class="relative group">
                    <div class="block p-6 bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg rounded-xl shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-200 border border-gray-200/20 dark:border-gray-700/20">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <a href="{{ route('notebooks.show', $notebook) }}" class="block">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $notebook->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $notebook->description ?? 'No description' }}
                                    </p>
                                </a>
                            </div>
                            <div class="relative ml-2">
                                <button id="menu-button-{{ $notebook->id }}" type="button" onclick="event.stopPropagation(); toggleMenu('{{ $notebook->id }}')" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 rounded-full hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                <div id="menu-{{ $notebook->id }}" class="hidden notebook-menu absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg ring-1 ring-black/5 z-10 border border-gray-200/20 dark:border-gray-700/20">
                                    <div class="py-1">
                                        <button onclick="openModal('edit-notebook-{{ $notebook->id }}'); toggleMenu('{{ $notebook->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50">
                                            Edit
                                        </button>
                                        <button onclick="openModal('confirm-notebook-deletion-{{ $notebook->id }}'); toggleMenu('{{ $notebook->id }}')" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100/50 dark:hover:bg-gray-700/50">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('notebooks.show', $notebook) }}" class="block">
                            <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $notebook->updated_at->diffForHumans() }}
                                <span class="mx-2 text-gray-300 dark:text-gray-600">•</span>
                                <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $notebook->notes_count ?? 0 }}</span> <span class="ml-0.5">notes</span>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- List View -->
            <div id="list-view" style="{{ count($notebooks) ? 'display: none;' : '' }}" class="overflow-hidden rounded-xl bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg shadow-sm border border-gray-200/20 dark:border-gray-700/20">
                <ul role="list" class="divide-y divide-gray-200/20 dark:divide-gray-700/20">
                    @foreach ($notebooks as $notebook)
                    <li class="relative hover:bg-gray-50/30 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="block px-6 py-4">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('notebooks.show', $notebook) }}" class="flex-1 min-w-0 group">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">{{ $notebook->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $notebook->description ?? 'No description' }}</p>
                                </a>
                                <div class="ml-4 flex-shrink-0 relative">
                                    <button id="menu-button-list-{{ $notebook->id }}" type="button" onclick="event.stopPropagation(); toggleMenu('list-{{ $notebook->id }}')" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 rounded-full hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div id="menu-list-{{ $notebook->id }}" class="hidden notebook-menu absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg ring-1 ring-black/5 z-10 border border-gray-200/20 dark:border-gray-700/20">
                                        <div class="py-1">
                                            <button onclick="openModal('edit-notebook-{{ $notebook->id }}'); toggleMenu('list-{{ $notebook->id }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50">
                                                Edit
                                            </button>
                                            <button onclick="openModal('confirm-notebook-deletion-{{ $notebook->id }}'); toggleMenu('list-{{ $notebook->id }}')" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100/50 dark:hover:bg-gray-700/50">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('notebooks.show', $notebook) }}" class="block">
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $notebook->updated_at->diffForHumans() }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $notebook->notes_count ?? 0 }}</span> <span class="ml-0.5">notes</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Create Notebook Modal -->
    <div id="create-notebook" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full">
            <form method="POST" action="{{ route('notebooks.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Create New Notebook') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Add a new notebook to organize your notes.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required autofocus placeholder="Enter notebook title" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="mt-6">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 shadow-sm" placeholder="Optional description"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" data-close-modal class="inline-flex items-center px-4 py-2 bg-white/20 dark:bg-gray-800/40 backdrop-blur-lg border border-gray-200/20 dark:border-gray-500/20 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-white/30 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-blue-600/90 backdrop-blur-sm border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Notebook Modals -->
    @foreach ($notebooks as $notebook)
        <div id="edit-notebook-{{ $notebook->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg rounded-xl shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Edit Notebook') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Update your notebook details.') }}
                    </p>

                    <form method="POST" action="{{ route('notebooks.update', $notebook) }}" class="mt-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="title-{{ $notebook->id }}" :value="__('Title')" />
                            <x-text-input id="title-{{ $notebook->id }}" name="title" type="text" class="mt-1 block w-full" :value="old('title', $notebook->title)" required placeholder="Enter notebook title" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="description-{{ $notebook->id }}" :value="__('Description')" />
                            <textarea id="description-{{ $notebook->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 shadow-sm" placeholder="Optional description">{{ old('description', $notebook->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button
                                type="button"
                                onclick="openModal('confirm-notebook-deletion-{{ $notebook->id }}'); closeModal('edit-notebook-{{ $notebook->id }}')"
                                class="inline-flex items-center px-4 py-2 bg-red-600/90 backdrop-blur-sm border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >{{ __('Delete') }}</button>

                            <div class="flex">
                                <button type="button" data-close-modal class="inline-flex items-center px-4 py-2 bg-white/20 dark:bg-gray-800/40 backdrop-blur-lg border border-gray-200/20 dark:border-gray-500/20 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-white/30 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Cancel') }}
                                </button>

                                <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-blue-600/90 backdrop-blur-sm border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="confirm-notebook-deletion-{{ $notebook->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg rounded-xl shadow-xl max-w-md w-full">
                <form method="POST" action="{{ route('notebooks.destroy', $notebook) }}" class="p-6">
                    @csrf
                    @method('DELETE')

                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Are you sure you want to delete this notebook?') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Once this notebook is deleted, all of its resources and data will be permanently deleted.') }}
                    </p>

                    <div class="mt-6">
                        <div class="p-4 mb-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/20 dark:border-red-900/30">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-300">{{ __('This action cannot be undone') }}</h3>
                            </div>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                                <p>{{ __('All notes within this notebook will also be deleted.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" data-close-modal class="inline-flex items-center px-4 py-2 bg-white/20 dark:bg-gray-800/40 backdrop-blur-lg border border-gray-200/20 dark:border-gray-500/20 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-white/30 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </button>

                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-red-600/90 backdrop-blur-sm border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Delete Notebook') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-app-layout>
