@extends('layouts.technician')

@section('title', __('dashboard.technician_title'))
@section('page_title', __('dashboard.title'))

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.technician_subtitle') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <x-stat-card label="{{ __('tickets.assigned_tickets') }}" :value="$stats['total']" color="primary" />
        <x-stat-card label="{{ __('dashboard.in_progress') }}" :value="$stats['in_progress']" color="amber" />
        <x-stat-card label="{{ __('dashboard.resolved') }}" :value="$stats['resolved']" color="emerald" />
        <x-stat-card label="{{ __('dashboard.closed') }}" :value="$stats['closed']" color="slate" />
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('tickets.assigned_tickets') }}</h2>
            <a href="{{ route('technician.tickets.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                {{ __('common.view_all') }} →
            </a>
        </div>

        @if($recentTickets->isEmpty())
            <x-empty-state
                title="{{ __('tickets.no_assigned') }}"
                description="{{ __('tickets.no_history') }}"
            />
        @else
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.ticket_id') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.subject') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.priority') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.status') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.requester') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.updated') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentTickets as $ticket)
                            <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('technician.tickets.show', $ticket) }}'">
                                <td class="px-6 py-4"><span class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-900 font-medium">{{ Str::limit($ticket->title, 40) }}</span></td>
                                <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-500">{{ $ticket->user?->name ?? '—' }}</span></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-500">{{ $ticket->updated_at->format('M d, Y') }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @foreach($recentTickets as $ticket)
                    <a href="{{ route('technician.tickets.show', $ticket) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <span class="text-xs font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                                <p class="text-sm font-medium text-gray-900 mt-0.5 truncate">{{ $ticket->title }}</p>
                            </div>
                            <x-status-badge :status="$ticket->status" />
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <x-priority-badge :priority="$ticket->priority" />
                            <span>{{ $ticket->user?->name ?? '—' }}</span>
                            <span>{{ $ticket->updated_at->format('M d') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
