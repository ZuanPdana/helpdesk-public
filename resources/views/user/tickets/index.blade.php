@extends('layouts.user')

@section('title', __('tickets.my_tickets'))
@section('page_title', __('tickets.my_tickets'))
@section('breadcrumb')
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('tickets.my_tickets') }}</span>
@endsection

@section('content')
    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <form method="GET" action="{{ route('user.tickets.index') }}" class="p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('tickets.search_placeholder') }}"
                            class="block w-full rounded-lg border border-gray-300 pl-10 pr-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                        >
                    </div>
                </div>

                {{-- Status filter --}}
                <select name="status" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition">
                    <option value="">{{ __('tickets.all_status') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>

                {{-- Priority filter --}}
                <select name="priority" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition">
                    <option value="">{{ __('tickets.all_priority') }}</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->value }}" {{ request('priority') === $priority->value ? 'selected' : '' }}>
                            {{ $priority->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors shrink-0">
                    {{ __('common.filter') }}
                </button>

                @if(request()->hasAny(['search', 'status', 'priority']))
                    <a href="{{ route('user.tickets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors shrink-0 text-center">
                        {{ __('tickets.clear') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tickets --}}
    <div class="bg-white rounded-xl border border-gray-200">
        @if($tickets->isEmpty())
            @if(request()->hasAny(['search', 'status', 'priority']))
                <x-empty-state
                    title="{{ __('tickets.no_tickets_match') }}"
                    description="{{ __('tickets.adjust_filters') }}"
                />
            @else
                <x-empty-state
                    title="{{ __('tickets.no_tickets_yet') }}"
                    description="{{ __('tickets.empty_description') }}"
                    :actionUrl="route('user.tickets.create')"
                    actionLabel="{{ __('tickets.create_first') }}"
                />
            @endif
        @else
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                                <a href="{{ route('user.tickets.index', array_merge(request()->query(), ['sort' => 'ticket_number', 'direction' => request('sort') === 'ticket_number' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">
                                    {{ __('tickets.ticket_id') }}
                                    @if(request('sort') === 'ticket_number')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ request('direction') === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}" /></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                                <a href="{{ route('user.tickets.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">
                                    {{ __('tickets.subject') }}
                                    @if(request('sort') === 'title')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ request('direction') === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}" /></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.priority') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.status') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.technician') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                                <a href="{{ route('user.tickets.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">
                                    {{ __('common.date') }}
                                    @if(request('sort') === 'created_at' || !request('sort'))
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="{{ request('direction') === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}" /></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900 font-medium">{{ Str::limit($ticket->title, 45) }}</span>
                                </td>
                                <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $ticket->assignee?->name ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('user.tickets.show', $ticket) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                        {{ __('common.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($tickets as $ticket)
                    <a href="{{ route('user.tickets.show', $ticket) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition-colors">
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
                            <span>{{ $ticket->assignee?->name ?? __('tickets.unassigned') }}</span>
                            <span class="text-gray-300">·</span>
                            <span>{{ $ticket->created_at->format('M d, Y') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
