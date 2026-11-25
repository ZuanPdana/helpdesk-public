@extends('layouts.admin')

@section('title', __('tickets.manage_tickets'))
@section('page_title', __('tickets.manage_tickets'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('tickets.tickets') }}</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('tickets.manage_tickets') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('tickets.manage_tickets_desc') }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <form method="GET" action="{{ route('admin.tickets.index') }}" class="grid grid-cols-1 gap-3 p-4 xl:grid-cols-6">
                <div class="xl:col-span-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('dashboard.search_tickets') }}" class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
                <select name="status" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('tickets.filter_all') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <select name="priority" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('tickets.filter_all') }}</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->value }}" @selected(request('priority') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </select>
                <select name="technician" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}" @selected((string) request('technician') === (string) $technician->id)>{{ $technician->name }}</option>
                    @endforeach
                </select>
                <select name="requester" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach($requesters as $requester)
                        <option value="{{ $requester->id }}" @selected((string) request('requester') === (string) $requester->id)>{{ $requester->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                <div class="flex gap-3 xl:col-span-2">
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('common.filter') }}</button>
                    @if(request()->hasAny(['search', 'status', 'priority', 'technician', 'requester', 'from_date', 'to_date']))
                        <a href="{{ route('admin.tickets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50">{{ __('common.reset') }}</a>
                    @endif
                </div>
            </form>

            @if($tickets->isEmpty())
                <x-empty-state title="{{ __('tickets.no_tickets') }}" description="{{ __('tickets.no_tickets_desc') }}" />
            @else
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                @php
                                    $sorts = [
                                            'ticket_number' => __('tickets.ticket_number'),
                                            'title' => __('tickets.title'),
                                            'priority' => __('tickets.priority_label_dt'),
                                            'status' => __('tickets.status_label'),
                                            'created_at' => __('tickets.created_date'),
                                            'updated_at' => __('common.updated'),
                                        ];
                                @endphp
                                @foreach($sorts as $field => $label)
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        <a href="{{ route('admin.tickets.index', array_merge(request()->query(), ['sort' => $field, 'direction' => request('sort') === $field && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">{{ $label }}</a>
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('tickets.requester') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('tickets.assigned_technician') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('tickets.action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($tickets as $ticket)
                                <tr class="transition-colors hover:bg-gray-50/50">
                                    <td class="px-6 py-4 text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 36) }}</td>
                                    <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                    <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->assignee?->name ?? __('tickets.unassigned') }}</td>
                                    <td class="px-6 py-4"><a href="{{ route('admin.tickets.show', $ticket) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('common.view') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-gray-100 md:hidden">
                    @foreach($tickets as $ticket)
                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="block px-4 py-4 transition-colors hover:bg-gray-50/50">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->title }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $ticket->user->name }} · {{ $ticket->created_at->format('M d, Y') }}</p>
                                </div>
                                <x-status-badge :status="$ticket->status" />
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                <x-priority-badge :priority="$ticket->priority" />
                                <span>{{ $ticket->assignee?->name ?? 'Unassigned' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($tickets->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
