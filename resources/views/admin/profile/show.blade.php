@extends('layouts.admin')

@section('title', 'Profile')
@section('page_title', 'Profile')
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">Profile</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <x-user-avatar :user="$admin" size="xl" class="ring-2 ring-primary-100" />
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $admin->name }}</h1>
                        <x-role-badge :role="$admin->role" />
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $admin->email }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $admin->phone ?: 'No phone number' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-6 xl:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">Profile Information</h2>
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $admin->name) }}" class="block w-full rounded-lg border border-gray-300 {{ $errors->has('name') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" class="block w-full rounded-lg border border-gray-300 {{ $errors->has('email') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $admin->phone) }}" class="block w-full rounded-lg border border-gray-300 {{ $errors->has('phone') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="department" class="mb-1.5 block text-sm font-medium text-gray-700">Department</label>
                            <input id="department" type="text" name="department" value="{{ old('department', $admin->department) }}" class="block w-full rounded-lg border border-gray-300 {{ $errors->has('department') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            @error('department')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="avatar" class="mb-1.5 block text-sm font-medium text-gray-700">Profile Photo</label>
                        <div class="flex items-center gap-4">
                            <x-user-avatar :user="$admin" size="md" class="ring-2 ring-gray-100" />
                            <div class="flex-1">
                                <input
                                    id="avatar"
                                    type="file"
                                    name="avatar"
                                    accept="image/jpeg,image/png,image/gif,image/webp"
                                    class="block w-full rounded-lg border border-gray-300 {{ $errors->has('avatar') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-1 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                                >
                                <p class="mt-1.5 text-xs text-gray-400">JPG, PNG, GIF, or WebP · Max 2 MB</p>
                                @error('avatar')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">Save Profile</button>
                    </div>
                </form>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
                <form method="POST" action="{{ route('admin.profile.password.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">Current Password</label>
                        <input id="current_password" type="password" name="current_password" class="block w-full rounded-lg border border-gray-300 {{ $errors->has('current_password') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        @error('current_password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">New Password</label>
                        <input id="password" type="password" name="password" class="block w-full rounded-lg border border-gray-300 {{ $errors->has('password') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800">Update Password</button>
                </form>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">Recent Submitted Tickets</h2>
            </div>
            @if($recentTickets->isEmpty())
                <x-empty-state title="No submitted tickets" description="This admin account has not submitted any tickets." />
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($recentTickets as $ticket)
                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->title }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-priority-badge :priority="$ticket->priority" />
                                <x-status-badge :status="$ticket->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
