@extends('layouts.user')

@section('title', __('profile.my_profile'))
@section('page_title', __('profile.my_profile'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('profile.profile') }}</span>
@endsection

@section('content')
    <div class="space-y-6">

        {{-- Profile header card --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <x-user-avatar :user="$user" size="xl" class="ring-2 ring-primary-100" />
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="mt-1 text-sm text-gray-400">{{ $user->phone ?: __('profile.no_phone_added') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- Profile information form --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 xl:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">{{ __('profile.profile_section_title') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('profile.profile_section_desc') }}</p>

                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Read-only email --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.email') }}</label>
                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5">
                            <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $user->email }}</span>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">{{ __('profile.email_locked_help') }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.name') }} <span class="text-red-500">*</span></label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="form-control"
                            >
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.phone') }}</label>
                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="{{ __('profile.phone_placeholder') }}"
                                class="form-control"
                            >
                            @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Avatar upload --}}
                    <div>
                        <label for="avatar" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.avatar') }}</label>
                        <div class="flex items-center gap-4">
                            <x-user-avatar :user="$user" size="md" class="ring-2 ring-gray-100" />
                            <div class="flex-1">
                                <input
                                    id="avatar"
                                    type="file"
                                    name="avatar"
                                    aria-invalid="{{ $errors->has('avatar') ? 'true' : 'false' }}"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                    class="form-file-control file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-1 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100"
                                >
                                <p class="mt-1.5 text-xs text-gray-400">{{ __('profile.max_photo_size') }}</p>
                                @error('avatar')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                            {{ __('profile.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change password --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">{{ __('profile.change_password') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('profile.change_password_strong') }}</p>

                <form method="POST" action="{{ route('user.profile.password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.current_password') }}</label>
                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            aria-invalid="{{ $errors->has('current_password') ? 'true' : 'false' }}"
                            autocomplete="current-password"
                            class="form-control"
                        >
                        @error('current_password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.new_password') }}</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            autocomplete="new-password"
                            class="form-control"
                        >
                        @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('profile.confirm_password') }}</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="form-control"
                        >
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800">
                            {{ __('profile.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Recent tickets --}}
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">{{ __('profile.recent_tickets') }}</h2>
            </div>

            @if($recentTickets->isEmpty())
                <x-empty-state
                    title="{{ __('profile.no_tickets') }}"
                    description="{{ __('profile.no_tickets_yet') }}"
                    action-url="{{ route('user.tickets.create') }}"
                    action-label="{{ __('profile.create_ticket_action') }}"
                />
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($recentTickets as $ticket)
                        <a href="{{ route('user.tickets.show', $ticket) }}" class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="text-xs font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->title }}</p>
                                <p class="mt-0.5 text-xs text-gray-400">{{ $ticket->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <x-priority-badge :priority="$ticket->priority" />
                                <x-status-badge :status="$ticket->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 px-6 py-3">
                    <a href="{{ route('user.tickets.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('profile.view_all_tickets') }}</a>
                </div>
            @endif
        </div>

    </div>
@endsection
