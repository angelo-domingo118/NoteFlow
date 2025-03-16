<!-- Edit Notebook Modal -->
<x-modal name="edit-notebook">
    <form method="POST" action="{{ route('notebooks.update', $notebook) }}" class="p-6">
        @csrf
        @method('PATCH')

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Notebook') }}</h2>

        <div class="mt-6">
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $notebook->title)" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="description" :value="__('Description')" />
            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="3">{{ old('description', $notebook->description) }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ml-3">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>

<!-- Add Note Modal -->
<x-modal name="add-note">
    <form method="POST" action="{{ route('notes.store', $notebook) }}" class="p-6" x-data="{ submitted: false }" @submit="setTimeout(() => $dispatch('close'), 100)">
        @csrf

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('New Note') }}</h2>

        <div class="mt-6">
            <x-input-label for="title-new" :value="__('Title')" />
            <x-text-input id="title-new" name="title" type="text" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="content-new" :value="__('Content')" />
            <textarea id="content-new" name="content" class="tinymce-editor mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="6"></textarea>
            <x-input-error :messages="$errors->get('content')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ml-3">
                {{ __('Create') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>

<!-- Add Source Modal -->
<x-modal name="add-source" :show="false" focusable :maxWidth="'6xl'">
    <form id="add-source-form" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100">
                {{ __('Add Source') }}
            </h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>
        
        <div class="flex flex-row gap-6">
            <!-- Left Column - Source Type Selection -->
            <div class="w-1/3">
                <div class="mb-4">
                    <h3 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-2">{{ __('Select Source Type') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Choose the type of source you want to add to your notebook.') }}
                    </p>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                            <input type="radio" name="type" value="text" class="sr-only" checked>
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-2">
                                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Text</p>
                                        <p class="text-gray-500 dark:text-gray-400">Add text content directly</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                            <input type="radio" name="type" value="website" class="sr-only">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-2">
                                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">Website</p>
                                        <p class="text-gray-500 dark:text-gray-400">Import from a webpage</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                            <input type="radio" name="type" value="youtube" class="sr-only">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-2">
                                        <svg class="h-5 w-5 text-red-600 dark:text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">YouTube</p>
                                        <p class="text-gray-500 dark:text-gray-400">Import from YouTube</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                            <input type="radio" name="type" value="file" class="sr-only">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-2">
                                        <svg class="h-5 w-5 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">File</p>
                                        <p class="text-gray-500 dark:text-gray-400">Upload a file</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
            </div>
            
            <!-- Right Column - Source Details Entry and Information -->
            <div class="w-2/3">
                <div class="mb-4">
                    <h3 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-2">{{ __('Source Details') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Enter information about your source.') }}
                    </p>
                </div>
                
                <!-- Source Name -->
                <div class="mb-4">
                    <x-input-label for="source_name" :value="__('Source Name')" class="text-gray-300" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <input id="source_name" name="name" type="text" class="pl-10 block w-full rounded-md border-0 bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500" required placeholder="Enter a name for this source" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">A descriptive name helps you identify this source later</p>
                </div>

                <!-- Dynamic Fields -->
                <div id="source_content_field">
                    <x-input-label for="source_content" :value="__('Content')" class="text-gray-300" />
                    <div class="mt-1">
                        <textarea id="source_content" name="content" class="tinymce-editor block w-full rounded-md border-0 bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500" rows="6" placeholder="Enter your text content here..."></textarea>
                    </div>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div id="source_url_field" class="hidden">
                    <x-input-label for="source_url" :value="__('URL')" class="text-gray-300" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <input id="source_url" name="url" type="url" class="pl-10 block w-full rounded-md border-0 bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500" placeholder="https://..." />
                    </div>
                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter the complete URL including https://</p>
                </div>

                <div id="source_file_field" class="hidden">
                    <x-input-label for="source_file" :value="__('File')" />
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="source_file" class="relative cursor-pointer rounded-md font-medium text-blue-600 dark:text-blue-500 hover:text-blue-500 dark:hover:text-blue-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="source_file" name="file" type="file" class="sr-only" accept=".pdf">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PDF files only, up to 10MB
                            </p>
                            <p id="selected-file-name" class="text-sm text-blue-600 dark:text-blue-500 mt-2 hidden"></p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>
                
                <!-- Dynamic instructions based on source type -->
                <div class="mt-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 flex items-center mb-2">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="source-type-title">Text Source Information</span>
                        </h4>
                        
                        <div id="text-instructions">
                            <p class="text-sm text-blue-700 dark:text-blue-400 mb-2">
                                Add text content directly to your notebook. Useful for:
                            </p>
                            <ul class="text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                                <li>Personal notes or observations</li>
                                <li>Copy-pasted content from elsewhere</li>
                                <li>Custom information you want to reference</li>
                            </ul>
                        </div>
                        
                        <div id="website-instructions" class="hidden">
                            <p class="text-sm text-blue-700 dark:text-blue-400 mb-2">
                                Import content from a webpage. The system will:
                            </p>
                            <ul class="text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                                <li>Extract the main content from the URL</li>
                                <li>Remove ads and unnecessary elements</li>
                                <li>Process up to 25,000 characters</li>
                            </ul>
                            <p class="text-sm text-blue-700 dark:text-blue-400 mt-2">
                                <strong>Note:</strong> Some websites may block content extraction
                            </p>
                        </div>
                        
                        <div id="youtube-instructions" class="hidden">
                            <p class="text-sm text-blue-700 dark:text-blue-400 mb-2">
                                Import content from a YouTube video. Requirements:
                            </p>
                            <ul class="text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                                <li>Standard YouTube URL (youtube.com/watch?v= or youtu.be/)</li>
                                <li>Public or unlisted videos only</li>
                                <li>Videos with available transcripts work best</li>
                            </ul>
                            <p class="text-sm text-blue-700 dark:text-blue-400 mt-2">
                                <strong>Format:</strong> https://www.youtube.com/watch?v=VIDEO_ID
                            </p>
                        </div>
                        
                        <div id="file-instructions" class="hidden">
                            <p class="text-sm text-blue-700 dark:text-blue-400 mb-2">
                                Upload a file to extract its content. Limitations:
                            </p>
                            <ul class="text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                                <li>PDF files only</li>
                                <li>Maximum file size: 10MB</li>
                                <li>Text-based PDFs work best (scanned documents may not extract well)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6 flex justify-end space-x-3">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ml-3" type="submit">
                <span id="submit-text">{{ __('Add Source') }}</span>
                <div id="loading-spinner" class="hidden ml-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </x-primary-button>
        </div>
    </form>
</x-modal>

<!-- Edit Note Modals -->
@foreach($notes as $note)
    <x-modal name="edit-note-{{ $note->id }}">
        <form method="POST" action="{{ route('notes.update', $note) }}" class="p-6" x-data="{ submitted: false }" @submit="setTimeout(() => $dispatch('close'), 100)">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Note') }}</h2>

            <div class="mt-6">
                <x-input-label for="title-{{ $note->id }}" :value="__('Title')" />
                <x-text-input id="title-{{ $note->id }}" name="title" type="text" class="mt-1 block w-full" :value="old('title', $note->title)" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="content-{{ $note->id }}" :value="__('Content')" />
                <textarea id="content-{{ $note->id }}" name="content" class="tinymce-editor mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="6">{{ old('content', $note->content) }}</textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
@endforeach

<!-- Edit Source Modals -->
@foreach($sources as $source)
    <x-modal name="edit-source-{{ $source->id }}">
        <form method="POST" action="{{ route('sources.update', $source) }}" class="p-6" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Edit Source') }}</h2>

            <div class="mt-6">
                <x-input-label for="name-{{ $source->id }}" :value="__('Name')" />
                <x-text-input id="name-{{ $source->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $source->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            @if($source->isText())
                <div class="mt-6">
                    <x-input-label for="content-{{ $source->id }}" :value="__('Content')" />
                    <textarea id="content-{{ $source->id }}" name="content" class="tinymce-editor mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="10">{{ old('content', $source->data) }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>
            @elseif($source->isWebsite())
                @php
                    $websiteContent = $source->getWebsiteContent();
                @endphp
                <div class="mt-6">
                    <x-input-label for="url-{{ $source->id }}" :value="__('URL')" />
                    <x-text-input id="url-{{ $source->id }}" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" value="{{ $websiteContent['url'] ?? $source->data }}" readonly />
                </div>
                
                @if($source->hasExtractionError())
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ __('Extraction Failed') }}</h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <p>{{ $source->getExtractionError() }}</p>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" name="retry_extraction" value="1" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        {{ __('Retry Extraction') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(!$websiteContent['content'])
                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('Extraction in Progress') }}</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>{{ __('The content is being extracted from the website. This may take a few moments.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6">
                        <x-input-label for="extracted-content-{{ $source->id }}" :value="__('Extracted Content')" />
                        <textarea id="extracted-content-{{ $source->id }}" name="extracted_content" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="10">{{ $websiteContent['content'] }}</textarea>
                        <x-input-error :messages="$errors->get('extracted_content')" class="mt-2" />
                    </div>
                    
                    @if(!empty($websiteContent['metadata']))
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Metadata') }}</h3>
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                @foreach($websiteContent['metadata'] as $key => $value)
                                    @if(!empty($value))
                                        <div class="mb-1">
                                            <span class="font-medium">{{ ucfirst($key) }}:</span> {{ $value }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @elseif($source->isYouTube())
                @php
                    $youtubeContent = $source->getYouTubeContent();
                @endphp
                <div class="mt-6">
                    <x-input-label for="youtube-url-{{ $source->id }}" :value="__('YouTube URL')" />
                    <x-text-input id="youtube-url-{{ $source->id }}" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" value="{{ $youtubeContent['url'] ?? $source->data }}" readonly />
                </div>
                
                @if($source->hasExtractionError())
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ __('Extraction Failed') }}</h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <p>{{ $source->getExtractionError() }}</p>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" name="retry_extraction" value="1" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        {{ __('Retry Extraction') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(empty($youtubeContent['transcript']) && empty($youtubeContent['plain_text']) && !isset($youtubeContent['error']))
                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('Extraction in Progress') }}</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>{{ __('The transcript is being extracted from the YouTube video. This may take a few moments.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-4">
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe 
                                src="https://www.youtube.com/embed/{{ $youtubeContent['video_id'] ?? preg_replace('/^.*(?:youtu.be\/|v\/|vi\/|u\/\w+\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/', '$1', $source->data) }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                class="rounded-md"
                            ></iframe>
                        </div>
                    </div>
                    
                    @if(!empty($youtubeContent['title']) || !empty($youtubeContent['author']))
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Video Information') }}</h3>
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                @if(!empty($youtubeContent['title']))
                                    <div class="mb-1">
                                        <span class="font-medium">{{ __('Title') }}:</span> {{ $youtubeContent['title'] }}
                                    </div>
                                @endif
                                @if(!empty($youtubeContent['author']))
                                    <div class="mb-1">
                                        <span class="font-medium">{{ __('Author') }}:</span> {{ $youtubeContent['author'] }}
                                    </div>
                                @endif
                                @if(!empty($youtubeContent['language_used']))
                                    <div class="mb-1">
                                        <span class="font-medium">{{ __('Language') }}:</span> {{ strtoupper($youtubeContent['language_used']) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if(!empty($youtubeContent['plain_text']))
                        <div class="mt-6">
                            <x-input-label for="extracted-content-{{ $source->id }}" :value="__('Transcript')" />
                            <textarea id="extracted-content-{{ $source->id }}" name="extracted_content" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="10">{{ $youtubeContent['plain_text'] }}</textarea>
                            <x-input-error :messages="$errors->get('extracted_content')" class="mt-2" />
                        </div>
                    @endif
                    
                    @if(!empty($youtubeContent['transcript']))
                        <div class="mt-4">
                            <x-input-label for="transcript-{{ $source->id }}" :value="__('Transcript with Timestamps')" />
                            <div id="transcript-{{ $source->id }}" class="mt-1 p-3 max-h-60 overflow-y-auto block w-full rounded-md border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-mono text-sm">
                                {!! nl2br(e($youtubeContent['transcript'])) !!}
                            </div>
                        </div>
                    @endif
                @endif
            @elseif($source->isFile())
                <div class="mt-6">
                    <x-input-label for="file-path-{{ $source->id }}" :value="__('File')" />
                    <div class="mt-1 flex items-center">
                        <span class="block w-full rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800">
                            {{ basename($source->file_path) }}
                        </span>
                        <a href="{{ $source->getFileUrl() }}" target="_blank" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Download') }}
                        </a>
                    </div>
                </div>
                
                @if($source->file_type === 'application/pdf')
                    @php
                        $pdfContent = $source->getPdfContent();
                    @endphp
                    
                    @if(isset($pdfContent['error']))
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ __('Extraction Failed') }}</h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <p>{{ $pdfContent['error'] }}</p>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" name="retry_extraction" value="1" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            {{ __('Retry Extraction') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($pdfContent['processing'] || empty($pdfContent['content']))
                        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('Extraction in Progress') }}</h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>{{ __('The content is being extracted from the PDF. This may take a few moments.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-6">
                            <x-input-label for="extracted-content-{{ $source->id }}" :value="__('Extracted Content')" />
                            <textarea id="extracted-content-{{ $source->id }}" name="extracted_content" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="10">{{ $pdfContent['content'] }}</textarea>
                            <x-input-error :messages="$errors->get('extracted_content')" class="mt-2" />
                        </div>
                        
                        @if(!empty($pdfContent['pages']))
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('PDF Information') }}</h3>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="mb-1">
                                        <span class="font-medium">{{ __('Pages') }}:</span> {{ $pdfContent['pages'] }}
                                    </div>
                                    @if(!empty($pdfContent['extracted_at']))
                                        <div class="mb-1">
                                            <span class="font-medium">{{ __('Extracted At') }}:</span> {{ \Carbon\Carbon::parse($pdfContent['extracted_at'])->format('Y-m-d H:i:s') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                @elseif(Str::endsWith($source->file_path, ['.txt', '.md']))
                    <div class="mt-4">
                        <x-input-label for="file-content-{{ $source->id }}" :value="__('File Content')" />
                        <textarea id="file-content-{{ $source->id }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="10" readonly>{{ Storage::disk('public')->get($source->file_path) }}</textarea>
                    </div>
                @endif
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
@endforeach

<!-- Rename Source Modals -->
@foreach($sources as $source)
    <x-modal name="rename-source-{{ $source->id }}">
        <form method="POST" action="{{ route('sources.update', $source) }}" class="p-6">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Rename Source') }}</h2>

            <div class="mt-6">
                <x-input-label for="rename-{{ $source->id }}" :value="__('Name')" />
                <x-text-input id="rename-{{ $source->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $source->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
@endforeach

<!-- YouTube Video Preview Modals -->
@foreach($sources as $source)
    @if($source->isYoutube())
        @php
            $youtubeContent = $source->getYouTubeContent();
            $videoId = $youtubeContent['video_id'] ?? null;
            if (!$videoId) {
                $videoId = preg_match('/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/', $source->data, $matches) ? $matches[1] : null;
            }
            if ($videoId) {
                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
            }
        @endphp
        <x-modal name="view-youtube-{{ $source->id }}" max-width="4xl">
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $source->name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="w-full aspect-video bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden">
                            <iframe 
                                class="w-full h-full" 
                                src="{{ $embedUrl }}" 
                                title="{{ $source->name }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        
                        @if(!empty($youtubeContent['title']) || !empty($youtubeContent['author']))
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Video Information') }}</h3>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    @if(!empty($youtubeContent['title']))
                                        <div class="mb-1">
                                            <span class="font-medium">{{ __('Title') }}:</span> {{ $youtubeContent['title'] }}
                                        </div>
                                    @endif
                                    @if(!empty($youtubeContent['author']))
                                        <div class="mb-1">
                                            <span class="font-medium">{{ __('Author') }}:</span> {{ $youtubeContent['author'] }}
                                        </div>
                                    @endif
                                    @if(!empty($youtubeContent['language_used']))
                                        <div class="mb-1">
                                            <span class="font-medium">{{ __('Language') }}:</span> {{ strtoupper($youtubeContent['language_used']) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        @if($source->hasExtractionError())
                            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ __('Extraction Failed') }}</h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <p>{{ $source->getExtractionError() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif(empty($youtubeContent['transcript']) && empty($youtubeContent['plain_text']) && !isset($youtubeContent['error']))
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('Extraction in Progress') }}</h3>
                                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                            <p>{{ __('The transcript is being extracted from the YouTube video. This may take a few moments.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Transcript') }}</h3>
                            <div class="mt-2 p-3 h-96 overflow-y-auto block w-full rounded-md border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-sm">
                                @if(!empty($youtubeContent['transcript']))
                                    <div class="font-mono">
                                        {!! nl2br(e($youtubeContent['transcript'])) !!}
                                    </div>
                                @elseif(!empty($youtubeContent['plain_text']))
                                    <div>
                                        {!! nl2br(e($youtubeContent['plain_text'])) !!}
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No transcript available for this video.') }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Close') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>
    @endif
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sourceForm = document.getElementById('add-source-form');
        const sourceTypeInputs = sourceForm.querySelectorAll('input[name="type"]');
        const contentField = document.getElementById('source_content_field');
        const urlField = document.getElementById('source_url_field');
        const fileField = document.getElementById('source_file_field');
        const submitText = document.getElementById('submit-text');
        const loadingSpinner = document.getElementById('loading-spinner');
        const fileInput = document.getElementById('source_file');
        const selectedFileName = document.getElementById('selected-file-name');
        const notebookId = document.querySelector('[data-notebook-id]').dataset.notebookId;

        // Instruction elements
        const sourceTypeTitle = document.getElementById('source-type-title');
        const textInstructions = document.getElementById('text-instructions');
        const websiteInstructions = document.getElementById('website-instructions');
        const youtubeInstructions = document.getElementById('youtube-instructions');
        const fileInstructions = document.getElementById('file-instructions');

        // Set the form action dynamically
        sourceForm.action = `/notebooks/${notebookId}/sources`;

        // Handle source type change
        sourceTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Update visual selection
                sourceTypeInputs.forEach(radio => {
                    radio.closest('label').classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                });
                this.closest('label').classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

                // Show/hide relevant fields
                contentField.classList.add('hidden');
                urlField.classList.add('hidden');
                fileField.classList.add('hidden');

                // Hide all instruction panels
                textInstructions.classList.add('hidden');
                websiteInstructions.classList.add('hidden');
                youtubeInstructions.classList.add('hidden');
                fileInstructions.classList.add('hidden');

                switch(this.value) {
                    case 'text':
                        contentField.classList.remove('hidden');
                        textInstructions.classList.remove('hidden');
                        sourceTypeTitle.textContent = 'Text Source Information';
                        break;
                    case 'website':
                        urlField.classList.remove('hidden');
                        websiteInstructions.classList.remove('hidden');
                        sourceTypeTitle.textContent = 'Website Source Information';
                        break;
                    case 'youtube':
                        urlField.classList.remove('hidden');
                        youtubeInstructions.classList.remove('hidden');
                        sourceTypeTitle.textContent = 'YouTube Source Information';
                        break;
                    case 'file':
                        fileField.classList.remove('hidden');
                        fileInstructions.classList.remove('hidden');
                        sourceTypeTitle.textContent = 'File Source Information';
                        break;
                }
            });
        });

        // Show selected file name
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validate file type
                    if (file.type !== 'application/pdf') {
                        alert('Please select a PDF file.');
                        this.value = '';
                        selectedFileName.classList.add('hidden');
                        return;
                    }
                    
                    // Validate file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File size must be less than 10MB.');
                        this.value = '';
                        selectedFileName.classList.add('hidden');
                        return;
                    }
                    
                    selectedFileName.textContent = file.name;
                    selectedFileName.classList.remove('hidden');
                } else {
                    selectedFileName.classList.add('hidden');
                }
            });
        }
        
        // Initialize - select the default option (text)
        const defaultTypeInput = document.querySelector('input[name="type"][value="text"]');
        if (defaultTypeInput) {
            defaultTypeInput.closest('label').classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        }

        sourceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form based on selected type
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            
            if (selectedType === 'file' && (!fileInput.files || fileInput.files.length === 0)) {
                alert('Please select a file to upload.');
                return;
            }
            
            if (selectedType === 'website' || selectedType === 'youtube') {
                const urlInput = document.getElementById('source_url');
                if (!urlInput.value.trim()) {
                    alert('Please enter a valid URL.');
                    return;
                }
                
                if (selectedType === 'youtube' && !urlInput.value.match(/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/)) {
                    alert('Please enter a valid YouTube URL.');
                    return;
                }
            }
            
            // Show loading state
            submitText.textContent = 'Adding Source...';
            loadingSpinner.classList.remove('hidden');
            
            // Disable the submit button
            this.querySelector('button[type="submit"]').disabled = true;

            // Submit the form
            this.submit();
        });
    });
</script>
@endpush
