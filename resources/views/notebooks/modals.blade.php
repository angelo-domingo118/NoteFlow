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
<x-modal name="add-source" :show="false" focusable>
    <form id="add-source-form" class="p-6">
        @csrf
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Add Source') }}
            </h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="mt-6 space-y-6">
            <!-- Source Type Selection -->
            <div>
                <x-input-label for="source_type" :value="__('Source Type')" />
                <div class="mt-2 grid grid-cols-2 gap-3">
                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                        <input type="radio" name="type" value="text" class="sr-only" checked>
                        <div class="flex w-full items-center justify-between">
                            <div class="flex items-center">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">Text</p>
                                    <p class="text-gray-500 dark:text-gray-400">Add text content directly</p>
                                </div>
                            </div>
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                        <input type="radio" name="type" value="website" class="sr-only">
                        <div class="flex w-full items-center justify-between">
                            <div class="flex items-center">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">Website</p>
                                    <p class="text-gray-500 dark:text-gray-400">Import from a webpage</p>
                                </div>
                            </div>
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                        <input type="radio" name="type" value="youtube" class="sr-only">
                        <div class="flex w-full items-center justify-between">
                            <div class="flex items-center">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">YouTube</p>
                                    <p class="text-gray-500 dark:text-gray-400">Import from YouTube</p>
                                </div>
                            </div>
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                        <input type="radio" name="type" value="file" class="sr-only">
                        <div class="flex w-full items-center justify-between">
                            <div class="flex items-center">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">File</p>
                                    <p class="text-gray-500 dark:text-gray-400">Upload a file</p>
                                </div>
                            </div>
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <!-- Source Name -->
            <div>
                <x-input-label for="source_name" :value="__('Source Name')" />
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <x-text-input id="source_name" name="name" type="text" class="pl-10 block w-full" required placeholder="Enter a name for this source" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Dynamic Fields -->
            <div id="source_content_field">
                <x-input-label for="source_content" :value="__('Content')" />
                <div class="mt-1">
                    <textarea id="source_content" name="content" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="6" placeholder="Enter your text content here..."></textarea>
                </div>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div id="source_url_field" class="hidden">
                <x-input-label for="source_url" :value="__('URL')" />
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <x-text-input id="source_url" name="url" type="url" class="pl-10 block w-full" placeholder="https://..." />
                </div>
                <x-input-error :messages="$errors->get('url')" class="mt-2" />
            </div>

            <div id="source_file_field" class="hidden">
                <x-input-label for="source_file" :value="__('File')" />
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="source_file" class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-500 hover:text-indigo-500 dark:hover:text-indigo-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a file</span>
                                <input id="source_file" name="file" type="file" class="sr-only" accept=".pdf,.txt,.md">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            PDF, TXT, or MD up to 10MB
                        </p>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
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
                    <textarea id="content-{{ $source->id }}" name="content" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="10">{{ old('content', $source->data) }}</textarea>
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
                <div class="mt-6">
                    <x-input-label for="youtube-url-{{ $source->id }}" :value="__('YouTube URL')" />
                    <x-text-input id="youtube-url-{{ $source->id }}" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" value="{{ $source->data }}" readonly />
                </div>
                
                <div class="mt-4">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe 
                            src="https://www.youtube.com/embed/{{ preg_replace('/^.*(?:youtu.be\/|v\/|e\/|u\/\w+\/|embed\/|v=)([^#\&\?]*).*/', '$1', $source->data) }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                            class="rounded-md"
                        ></iframe>
                    </div>
                </div>
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
                
                @if(Str::endsWith($source->file_path, ['.txt', '.md']))
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
            $videoId = preg_match('/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/', $source->data, $matches) ? $matches[1] : null;
            if ($videoId) {
                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
            }
        @endphp
        <x-modal name="view-youtube-{{ $source->id }}">
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $source->name }}</h2>
                
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

        // Handle source type change
        sourceTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Update visual selection
                sourceTypeInputs.forEach(radio => {
                    radio.closest('label').classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
                });
                this.closest('label').classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');

                // Show/hide relevant fields
                contentField.classList.add('hidden');
                urlField.classList.add('hidden');
                fileField.classList.add('hidden');

                switch(this.value) {
                    case 'text':
                        contentField.classList.remove('hidden');
                        break;
                    case 'website':
                    case 'youtube':
                        urlField.classList.remove('hidden');
                        break;
                    case 'file':
                        fileField.classList.remove('hidden');
                        break;
                }
            });
        });

        // Handle file drag and drop
        const dropZone = fileField.querySelector('.border-dashed');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
        }

        // Handle form submission
        sourceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            submitText.textContent = 'Adding Source...';
            loadingSpinner.classList.remove('hidden');
            
            // Disable the submit button
            this.querySelector('button[type="submit"]').disabled = true;

            // Create FormData object
            const formData = new FormData(this);
            formData.append('notebook_id', document.querySelector('[data-notebook-id]').dataset.notebookId);

            // Submit the form using fetch
            fetch(`/notebooks/${formData.get('notebook_id')}/sources`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Close the modal
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-source' }));
                    
                    // Reload the page to show the new source
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to add source');
                }
            })
            .catch(error => {
                console.error('Error adding source:', error);
                let errorMessage = 'Failed to add source';
                
                if (error.errors) {
                    // Handle validation errors
                    errorMessage = Object.values(error.errors).flat().join('\n');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert(errorMessage);
            })
            .finally(() => {
                // Reset the form state
                submitText.textContent = 'Add Source';
                loadingSpinner.classList.add('hidden');
                this.querySelector('button[type="submit"]').disabled = false;
            });
        });
    });
</script>
@endpush
