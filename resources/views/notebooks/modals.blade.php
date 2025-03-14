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
<x-modal name="add-source">
    <form method="POST" action="{{ route('sources.store', $notebook) }}" class="p-6" enctype="multipart/form-data" 
        x-data="{ 
            type: null,
            dragOver: false,
            handleDrop(e) {
                e.preventDefault();
                this.dragOver = false;
                const file = e.dataTransfer.files[0];
                if (file) {
                    document.getElementById('source_file').files = e.dataTransfer.files;
                    document.getElementById('source_name').value = file.name;
                }
            }
        }">
        @csrf

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Add Source') }}</h2>

        <div class="mt-6 space-y-6">
            <div>
                <x-input-label for="source_name" :value="__('Name')" />
                <x-text-input id="source_name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label :value="__('Source Type')" />
                <div class="mt-1 grid grid-cols-3 gap-3">
                    <label class="flex items-center justify-center rounded-md border-2 border-dashed p-4 cursor-pointer" :class="{'border-blue-500': type === 'file'}" @click="type = 'file'">
                        <input type="radio" name="type" value="file" class="sr-only">
                        <span class="text-sm text-gray-700 dark:text-gray-300">File Upload</span>
                    </label>
                    <label class="flex items-center justify-center rounded-md border-2 border-dashed p-4 cursor-pointer" :class="{'border-blue-500': type === 'link'}" @click="type = 'link'">
                        <input type="radio" name="type" value="link" class="sr-only">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Website/YouTube</span>
                    </label>
                    <label class="flex items-center justify-center rounded-md border-2 border-dashed p-4 cursor-pointer" :class="{'border-blue-500': type === 'text'}" @click="type = 'text'">
                        <input type="radio" name="type" value="text" class="sr-only">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Paste Text</span>
                    </label>
                </div>
            </div>

            <!-- File Upload Area -->
            <div x-show="type === 'file'" class="mt-4">
                <div @dragover.prevent="dragOver = true"
                    @dragleave.prevent="dragOver = false"
                    @drop.prevent="handleDrop"
                    :class="{ 'border-blue-500': dragOver }">
                    <label class="flex flex-col items-center justify-center w-full h-32 px-4 transition bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md appearance-none cursor-pointer hover:border-gray-400 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div class="flex flex-col items-center mt-4 space-y-1 text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Drop files to Attach, or</span>
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">browse</span>
                        </div>
                        <input type="file" id="source_file" name="file" class="hidden" accept=".pdf,.txt,.md">
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PDF, Text, or Markdown files</p>
            </div>

            <!-- Website/YouTube URL -->
            <div x-show="type === 'link'" class="mt-4">
                <x-input-label for="source_url" :value="__('URL')" />
                <x-text-input id="source_url" name="url" type="url" class="mt-1 block w-full" placeholder="https://" />
                <x-input-error :messages="$errors->get('url')" class="mt-2" />
            </div>

            <!-- Text Content -->
            <div x-show="type === 'text'" class="mt-4">
                <x-input-label for="source_data" :value="__('Content')" />
                <textarea id="source_data" name="data" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="6"></textarea>
                <x-input-error :messages="$errors->get('data')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ml-3">
                {{ __('Add') }}
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

<!-- Rename Source Modals -->
@foreach($sources as $source)
    <x-modal name="rename-source-{{ $source->id }}">
        <form method="POST" action="{{ route('sources.update', $source) }}" class="p-6">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Rename Source') }}</h2>

            <div class="mt-6">
                <x-input-label for="source_name_{{ $source->id }}" :value="__('Name')" />
                <x-text-input id="source_name_{{ $source->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $source->name)" required />
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
