@extends('layouts.technician')

@section('title', 'Assigned Tickets')
@section('page_title', 'Assigned Tickets')

@section('content')
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <form method="GET" action="{{ route('technician.tickets.index') }}" class="p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search tickets..."
                            class="block w-full rounded-lg border border-gray-300 pl-10 pr-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                        >
                    </div>
                </div>

                <select name="status" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors shrink-0">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('technician.tickets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors shrink-0 text-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        @if($tickets->isEmpty())
            <x-empty-state title="No assigned tickets" description="You're all caught up." />
        @else
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Ticket ID</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Subject</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Priority</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Status</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Requester</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Date</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4"><span class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-900 font-medium">{{ Str::limit($ticket->title, 45) }}</span></td>
                                <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-500">{{ $ticket->user?->name ?? '—' }}</span></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</span></td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('technician.tickets.show', $ticket) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @foreach($tickets as $ticket)
                    <a href="{{ route('technician.tickets.show', $ticket) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <span class="text-xs font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                                <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->title }}</p>
                            </div>
                            <x-status-badge :status="$ticket->status" />
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mt-2">
                            <x-priority-badge :priority="$ticket->priority" />
                            <span class="text-gray-300">·</span>
                            <span>{{ $ticket->user?->name ?? '—' }}</span>
                            <span class="text-gray-300">·</span>
                            <span>{{ $ticket->created_at->format('M d, Y') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('tickets.available_tickets') }}</h2>
        </div>

        @if($availableTickets->isEmpty())
            <x-empty-state title="{{ __('tickets.no_available') }}" />
        @else
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.ticket_number') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.title') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.priority_label_dt') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.status_label') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.requester') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.created_date') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($availableTickets as $ticket)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4"><span class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-900 font-medium">{{ Str::limit($ticket->title, 45) }}</span></td>
                                <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-500">{{ $ticket->user?->name ?? '—' }}</span></td>
                                <td class="px-6 py-4"><span class="text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</span></td>
                                <td class="px-6 py-4">
                                    @can('selfAssign', $ticket)
                                        <form action="{{ route('technician.tickets.assign-self', $ticket) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">{{ __('tickets.self_assign') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @foreach($availableTickets as $ticket)
                    <div class="px-4 py-4">
                        <a href="{{ route('technician.tickets.show', $ticket) }}" class="block">
                            <span class="text-xs font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $ticket->title }}</p>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mt-2">
                                <x-priority-badge :priority="$ticket->priority" />
                                <x-status-badge :status="$ticket->status" />
                                <span>{{ $ticket->user?->name ?? '—' }}</span>
                            </div>
                        </a>
                        @can('selfAssign', $ticket)
                            <form action="{{ route('technician.tickets.assign-self', $ticket) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">{{ __('tickets.self_assign') }}</button>
                            </form>
                        @endcan
                    </div>
                @endforeach
            </div>

            @if($availableTickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $availableTickets->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
