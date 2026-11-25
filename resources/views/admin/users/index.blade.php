@extends('layouts.admin')

@section('title', __('admin.manage_users'))
@section('page_title', __('admin.manage_users'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('admin.users') }}</span>
@endsection

@section('content')
    <div x-data="{ deleteDialogOpen: false, deleteAction: '', deleteName: '' }" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.manage_users') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('admin.manage_users_desc') }}</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                {{ __('dashboard.create_user') }}
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-3 p-4 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.search_users') }}" class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
                <select name="role" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('common.filter') }}</button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50">{{ __('common.reset') }}</a>
                    @endif
                </div>
            </form>

            @if($users->isEmpty())
                <x-empty-state title="{{ __('admin.no_users') }}" description="{{ __('admin.no_users_desc') }}" />
            @else
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                    @php
                                    $sorts = [
                                        'name' => __('profile.name'),
                                        'email' => __('profile.email'),
                                        'role' => __('profile.role'),
                                        'created_at' => __('common.created'),
                                    ];
                                @endphp
                                @foreach($sorts as $field => $label)
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => $field, 'direction' => request('sort') === $field && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">{{ $label }}</a>
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($users as $user)
                                <tr class="transition-colors hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700">{{ $user->initials() }}</div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->department ?: __('common.none') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4"><x-role-badge :role="$user->role" /></td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $user->accountStatusValue() === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                                            {{ $user->accountStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3 text-sm">
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-primary-600 hover:text-primary-700">View</a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-gray-700 hover:text-gray-900">Edit</a>
                                            <button type="button" @click="deleteName = @js($user->name); deleteAction = @js(route('admin.users.destroy', $user)); deleteDialogOpen = true" class="font-medium text-red-600 hover:text-red-700">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-gray-100 md:hidden">
                    @foreach($users as $user)
                        <div class="space-y-3 px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <x-role-badge :role="$user->role" />
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $user->accountStatusValue() === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">{{ $user->accountStatusLabel() }}</span>
                                <span class="text-xs text-gray-500">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-primary-600">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-gray-700">Edit</a>
                                <button type="button" @click="deleteName = @js($user->name); deleteAction = @js(route('admin.users.destroy', $user)); deleteDialogOpen = true" class="font-medium text-red-600">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($users->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>

        <div x-show="deleteDialogOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900">Delete User</h2>
                <p class="mt-2 text-sm text-gray-500">Are you sure you want to delete <span class="font-medium text-gray-900" x-text="deleteName"></span>? This action cannot be undone.</p>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="deleteDialogOpen = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">{{ __('common.cancel') }}</button>
                    <form method="POST" x-bind:action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">{{ __('admin.delete_user') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
