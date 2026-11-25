@extends('layouts.admin')

@section('title', $managedUser->name)
@section('page_title', 'User Detail')
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('admin.users.index') }}" class="hover:text-gray-700">Users</a>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ $managedUser->name }}</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 text-xl font-bold text-primary-700">{{ $managedUser->initials() }}</div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-bold text-gray-900">{{ $managedUser->name }}</h1>
                            <x-role-badge :role="$managedUser->role" />
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ $managedUser->email }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $managedUser->department ?: 'No department' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('common.edit') }}</a>
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">Back to Users</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-6 lg:col-span-1">
                <h2 class="text-base font-semibold text-gray-900">Account Information</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $managedUser->accountStatusLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $managedUser->phone ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $managedUser->department ?: 'Not assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $managedUser->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white lg:col-span-2">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Recent Submitted Tickets</h2>
                </div>
                @if($recentTickets->isEmpty())
                    <x-empty-state title="{{ __('admin.no_tickets_submitted') }}" description="{{ __('admin.no_tickets_submitted_desc') }}" />
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentTickets as $ticket)
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-gray-50">
                                <div class="min-w-0">
                                    <p class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->title }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-priority-badge :priority="$ticket->priority" />
                                    <x-status-badge :status="$ticket->status" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
