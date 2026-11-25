<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.sign_in') }} — {{ config('app.name', 'Helpdesk') }}</title>
    <meta name="description" content="Sign in to your helpdesk account">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased" x-data>
    <div class="flex min-h-dvh items-center justify-center px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="w-full max-w-md">
            {{-- Language switcher (top right) --}}
            <div class="flex justify-end mb-4">
                <x-language-switcher />
            </div>
            {{-- Logo --}}
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-xl bg-primary-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-600/25">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('auth.sign_in_title') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('auth.sign_in_subtitle') }}</p>
            </div>

            {{-- Login form --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('auth.email') }}</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="block w-full rounded-lg border border-gray-300 @error('email') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                            placeholder="{{ __('auth.email_placeholder') }}"
                        >
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('auth.password') }}</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-lg border border-gray-300 @error('password') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                            placeholder="{{ __('auth.password_placeholder') }}"
                        >
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500/20"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-600">{{ __('auth.remember_me') }}</label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                    >
                        {{ __('auth.sign_in_button') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>
