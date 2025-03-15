<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Profile Information -->
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <header class="mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                                <h2 class="text-base font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('Profile Information') }}
                                </h2>
                            </header>
                            
                            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                @csrf
                            </form>
                            
                            <form method="post" action="{{ route('profile.update') }}" class="space-y-3">
                                @csrf
                                @method('patch')
                                
                                <div>
                                    <x-input-label for="name" :value="__('Name')" class="text-sm font-medium" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-1 text-xs" :messages="$errors->get('name')" />
                                </div>
                                
                                <div>
                                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500" :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-1 text-xs" :messages="$errors->get('email')" />
                                </div>
                                
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                                        <p class="text-xs text-yellow-800 dark:text-yellow-200 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ __('Your email address is unverified.') }}
                                            
                                            <button form="send-verification" class="ml-2 underline text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                                {{ __('Verify') }}
                                            </button>
                                        </p>
                                        
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-xs text-green-600 dark:text-green-400 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ __('Verification link sent!') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-4 mt-3">
                                    <x-primary-button class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Save') }}
                                    </x-primary-button>
                                    
                                    @if (session('status') === 'profile-updated')
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition
                                            x-init="setTimeout(() => show = false, 2000)"
                                            class="text-xs text-green-600 dark:text-green-400 flex items-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ __('Saved.') }}
                                        </p>
                                    @endif
                                </div>
                            </form>
                        </div>
                        
                        <!-- Update Password -->
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <header class="mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                                <h2 class="text-base font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{ __('Update Password') }}
                                </h2>
                            </header>
                            
                            <form method="post" action="{{ route('password.update') }}" class="space-y-3">
                                @csrf
                                @method('put')
                                
                                <div>
                                    <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-sm font-medium" />
                                    <div class="relative mt-1">
                                        <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 pr-10" autocomplete="current-password" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer toggle-password">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-xs" />
                                </div>
                                
                                <div>
                                    <x-input-label for="update_password_password" :value="__('New Password')" class="text-sm font-medium" />
                                    <div class="relative mt-1">
                                        <x-text-input id="update_password_password" name="password" type="password" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 pr-10" autocomplete="new-password" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer toggle-password">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-xs" />
                                </div>
                                
                                <div>
                                    <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium" />
                                    <div class="relative mt-1">
                                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 pr-10" autocomplete="new-password" />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer toggle-password">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-xs" />
                                </div>
                                
                                <div class="flex items-center gap-4 mt-3">
                                    <x-primary-button class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Save') }}
                                    </x-primary-button>
                                    
                                    @if (session('status') === 'password-updated')
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition
                                            x-init="setTimeout(() => show = false, 2000)"
                                            class="text-xs text-green-600 dark:text-green-400 flex items-center"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ __('Saved.') }}
                                        </p>
                                    @endif
                                </div>
                            </form>
                        </div>
                        
                        <!-- Delete Account -->
                        <div class="bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4">
                            <header class="mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                                <h2 class="text-base font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    {{ __('Delete Account') }}
                                </h2>
                            </header>
                            
                            <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-200 dark:border-red-800/30">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-xs text-red-700 dark:text-red-400">
                                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. This action is irreversible.') }}
                                        </p>
                                        
                                        <div class="mt-3">
                                            <x-danger-button
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                                class="flex items-center text-sm py-1.5 px-3"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ __('Delete Account') }}
                                            </x-danger-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <div class="mb-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ __('Are you sure?') }}
                </h2>

                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                    {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Password') }}" class="text-sm font-medium" />

                <div class="relative mt-1">
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 pr-10"
                        placeholder="{{ __('Password') }}"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer toggle-password">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="flex items-center text-sm py-1.5 px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="flex items-center text-sm py-1.5 px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggles = document.querySelectorAll('.toggle-password');
            
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = this.parentElement.querySelector('input');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        `;
                    } else {
                        passwordInput.type = 'password';
                        this.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        `;
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
