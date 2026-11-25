@extends('layouts.admin')

@section('title', __('admin.manage_technicians'))
@section('page_title', __('admin.manage_technicians'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('admin.technicians') }}</span>
@endsection

@section('content')
    <div x-data="{ deleteDialogOpen: false, deleteAction: '', deleteName: '' }" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.manage_technicians') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('admin.manage_technicians_desc') }}</p>
            </div>
            <a href="{{ route('admin.technicians.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                {{ __('dashboard.create_technician') }}
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <form method="GET" action="{{ route('admin.technicians.index') }}" class="grid grid-cols-1 gap-3 p-4 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.search_technicians') }}" class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
                <select name="department" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach($departments as $department)
                        <option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>
                    @endforeach
                </select>
                <select name="availability" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach($availabilityOptions as $availability)
                        <option value="{{ $availability->value }}" @selected(request('availability') === $availability->value)>{{ $availability->label() }}</option>
                    @endforeach
                </select>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('common.filter') }}</button>
                    @if(request()->hasAny(['search', 'department', 'availability']))
                        <a href="{{ route('admin.technicians.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50">{{ __('common.reset') }}</a>
                    @endif
                </div>
            </form>

            @if($technicians->isEmpty())
                <x-empty-state title="{{ __('admin.no_technicians') }}" description="{{ __('admin.no_technicians_desc') }}" />
            @else
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                    @php
                                    $sorts = [
                                        'name' => __('profile.name'),
                                        'email' => __('profile.email'),
                                        'department' => __('profile.department'),
                                        'created_at' => __('common.created'),
                                    ];
                                @endphp
                                @foreach($sorts as $field => $label)
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        <a href="{{ route('admin.technicians.index', array_merge(request()->query(), ['sort' => $field, 'direction' => request('sort') === $field && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">{{ $label }}</a>
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Availability</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($technicians as $technician)
                                <tr class="transition-colors hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">{{ $technician->initials() }}</div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $technician->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $technician->phone ?: __('common.none') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->department ?: 'No department' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $technician->accountStatusValue() === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">{{ $technician->accountStatusLabel() }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $technician->availabilityValue() === 'busy' ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' }}">{{ $technician->availabilityLabel() }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3 text-sm">
                                            <a href="{{ route('admin.technicians.show', $technician) }}" class="font-medium text-primary-600 hover:text-primary-700">{{ __('common.view') }}</a>
                                            <a href="{{ route('admin.technicians.edit', $technician) }}" class="font-medium text-gray-700 hover:text-gray-900">{{ __('common.edit') }}</a>
                                            <button type="button" @click="deleteName = @js($technician->name); deleteAction = @js(route('admin.technicians.destroy', $technician)); deleteDialogOpen = true" class="font-medium text-red-600 hover:text-red-700">{{ __('common.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-gray-100 md:hidden">
                    @foreach($technicians as $technician)
                        <div class="space-y-3 px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $technician->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $technician->email }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $technician->availabilityValue() === 'busy' ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' }}">{{ $technician->availabilityLabel() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $technician->department ?: 'No department' }}</p>
                            <div class="flex items-center gap-3 text-sm">
                                <a href="{{ route('admin.technicians.show', $technician) }}" class="font-medium text-primary-600">View</a>
                                <a href="{{ route('admin.technicians.edit', $technician) }}" class="font-medium text-gray-700">Edit</a>
                                <button type="button" @click="deleteName = @js($technician->name); deleteAction = @js(route('admin.technicians.destroy', $technician)); deleteDialogOpen = true" class="font-medium text-red-600">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($technicians->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $technicians->links() }}
                    </div>
                @endif
            @endif
        </div>

        <div x-show="deleteDialogOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.delete_technician') }}</h2>
                <p class="mt-2 text-sm text-gray-500">{{ __('admin.delete_technician') }} <span class="font-medium text-gray-900" x-text="deleteName"></span>? {{ __('admin.delete_technician_confirm') ?? '' }}</p>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="deleteDialogOpen = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">{{ __('common.cancel') }}</button>
                    <form method="POST" x-bind:action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">{{ __('admin.delete_technician') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
