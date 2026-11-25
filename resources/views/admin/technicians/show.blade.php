@extends('layouts.admin')

@section('title', $technician->name)
@section('page_title', 'Technician Detail')
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('admin.technicians.index') }}" class="hover:text-gray-700">Technicians</a>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ $technician->name }}</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-xl font-bold text-amber-700">{{ $technician->initials() }}</div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $technician->name }}</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $technician->email }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $technician->department ?: 'No department' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.technicians.edit', $technician) }}" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('common.edit') }}</a>
                    <a href="{{ route('admin.technicians.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">Back to Technicians</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-base font-semibold text-gray-900">Technician Information</h2>
                    <dl class="mt-4 space-y-4">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $technician->phone ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $technician->accountStatusLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Availability</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $technician->availabilityLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Active Assignments</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $technician->active_assignments_count }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-base font-semibold text-gray-900">Skills</h2>
                    @if($technician->skillsList())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($technician->skillsList() as $skill)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-sm text-gray-500">No stored skills yet.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white lg:col-span-2">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">Recent Assigned Tickets</h2>
                </div>
                @if($assignedTickets->isEmpty())
                    <x-empty-state title="{{ __('admin.no_assignments') }}" description="{{ __('admin.no_assignments_desc') }}" />
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($assignedTickets as $ticket)
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-gray-50">
                                <div class="min-w-0">
                                    <p class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->title }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $ticket->user->name }} · {{ $ticket->created_at->format('M d, Y h:i A') }}</p>
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
