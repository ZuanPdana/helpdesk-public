<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Helpdesk') }}</title>
    <meta name="description" content="@yield('meta_description', 'Internal Helpdesk & Ticketing System')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false, profileOpen: false }">
    <div class="min-h-screen flex">
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            x-cloak
        ></div>

        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-slate-900 text-white sidebar-transition lg:static lg:z-auto lg:translate-x-0"
        >
            <div class="flex items-center gap-3 border-b border-slate-700/50 px-6 py-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-500">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-semibold tracking-tight">Helpdesk</h1>
                    <p class="text-xs text-slate-400">{{ $layoutSubtitle }}</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $layoutNavigationHeading }}</p>

                @foreach($layoutNavigation as $item)
                    @php
                        $isActive = collect($item['active'])->contains(fn (string $pattern): bool => request()->routeIs($pattern));
                    @endphp
                    <a
                        href="{{ $item['href'] }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ $isActive ? 'bg-primary-600/20 text-primary-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        @switch($item['icon'])
                            @case('dashboard')
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                                @break
                            @case('ticket')
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6.75V6a2.25 2.25 0 00-2.25-2.25h-4.5A2.25 2.25 0 007.5 6v.75m9 0V18A2.25 2.25 0 0114.25 20.25h-4.5A2.25 2.25 0 017.5 18V6.75m9 0h1.125A1.875 1.875 0 0119.5 8.625v.75A1.875 1.875 0 0117.625 11.25H16.5m0-4.5H7.5m0 0H6.375A1.875 1.875 0 004.5 8.625v.75a1.875 1.875 0 001.875 1.875H7.5" /></svg>
                                @break
                            @case('users')
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a8.964 8.964 0 003.741-2.372A6.474 6.474 0 0018 15.75V18.72zm0 0A9.023 9.023 0 0112 21a9.023 9.023 0 01-6-2.28m12 0V19.5A2.25 2.25 0 0115.75 21h-7.5A2.25 2.25 0 016 19.5v-.78m12 0a9.023 9.023 0 00-12 0M15 7.5a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM7.5 10.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                @break
                            @case('wrench')
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 3.206a1.125 1.125 0 011.159-.286 8.25 8.25 0 018.117 8.117 1.125 1.125 0 01-.286 1.159l-7.037 7.037a3.75 3.75 0 01-5.304 0l-2.965-2.965a3.75 3.75 0 010-5.304l7.037-7.037z" /></svg>
                                @break
                            @case('chart')
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125V20.25A1.125 1.125 0 016.375 21.375h-2.25A1.125 1.125 0 013 20.25v-7.125zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V20.25a1.125 1.125 0 01-1.125 1.125h-2.25A1.125 1.125 0 019.75 20.25V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125V20.25a1.125 1.125 0 01-1.125 1.125h-2.25A1.125 1.125 0 0116.5 20.25V4.125z" /></svg>
                                @break
                            @case('profile')
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 11-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                @break
                            @default
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        @endswitch
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-700/50 p-4">
                <div class="flex items-center gap-3">
                    <x-user-avatar :user="auth()->user()" size="sm" class="bg-primary-600 text-white" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-400 transition-colors hover:bg-slate-800 hover:text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        {{ $layoutLogoutLabel }}
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 border-b border-gray-200 bg-white">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-4">
                        <button @click="sidebarOpen = true" class="-ml-2 rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 lg:hidden" aria-label="Open navigation">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            @hasSection('breadcrumb')
                                <nav class="flex items-center gap-1.5 text-sm text-gray-500">
                                    <a href="{{ $layoutHomeRoute }}" class="hover:text-gray-700">{{ $layoutHomeLabel }}</a>
                                    @yield('breadcrumb')
                                </nav>
                            @endif
                            <h2 class="truncate text-lg font-semibold text-gray-900">@yield('page_title', 'Dashboard')</h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($layoutShowTopbarSearch)
                            <form method="GET" action="{{ $layoutTopbarSearchAction }}" class="hidden lg:block">
                            <label for="layout-search" class="sr-only">{{ __('common.search') }}</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <input
                                        id="layout-search"
                                        type="search"
                                        name="search"
                                        value="{{ $layoutTopbarSearchValue }}"
                                        placeholder="{{ $layoutTopbarSearchPlaceholder }}"
                                        class="block w-64 rounded-lg border border-gray-300 py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                                    >
                                </div>
                            </form>
                        @endif

                        @if($layoutShowNotifications)
                            <x-notifications.bell
                                :notifications="$shellNotifications"
                                :unread-count="$shellUnreadNotificationCount"
                                :read-route-name="$layoutNotificationReadRouteName"
                                :view-all-route="$layoutNotificationViewAllRoute"
                                :view-all-label="$layoutNotificationViewAllLabel"
                            />
                        @endif

                        <x-language-switcher />

                        <div class="hidden items-center gap-2 text-sm text-gray-500 sm:flex">
                            <x-user-avatar :user="auth()->user()" size="sm" />
                            <div class="hidden text-left xl:block">
                                <p class="font-medium text-gray-700">{{ auth()->user()->name }}</p>
                            </div>
                            @if($layoutShowRoleBadge)
                                <x-role-badge :role="auth()->user()->role" />
                            @endif
                        </div>

                        <div class="relative" @click.outside="profileOpen = false">
                            <button @click="profileOpen = !profileOpen" type="button" class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700" aria-label="Open profile menu">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 11-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="p-2">
                                    @if($layoutProfileRoute)
                                        <a href="{{ $layoutProfileRoute }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 11-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ __('profile.profile') }}
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                            </svg>
                                            {{ __('common.sign_out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                    <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-emerald-800">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                    <div class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            @endif

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
