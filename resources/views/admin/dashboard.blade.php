@extends('layouts.admin')

@section('title', __('dashboard.admin_title'))
@section('page_title', __('dashboard.admin_title'))

@section('content')
    <div class="space-y-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.admin_subtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                            {{ __('dashboard.create_user') }}
                    </a>
                    <a href="{{ route('admin.technicians.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 3.206a1.125 1.125 0 011.159-.286 8.25 8.25 0 018.117 8.117 1.125 1.125 0 01-.286 1.159l-7.037 7.037a3.75 3.75 0 01-5.304 0l-2.965-2.965a3.75 3.75 0 010-5.304l7.037-7.037z" />
                        </svg>
                            {{ __('dashboard.create_technician') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
            <x-stat-card label="{{ __('dashboard.total_tickets') }}" :value="$stats['total_tickets']" color="primary">
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6.75V6a2.25 2.25 0 00-2.25-2.25h-4.5A2.25 2.25 0 007.5 6v.75m9 0V18A2.25 2.25 0 0114.25 20.25h-4.5A2.25 2.25 0 017.5 18V6.75m9 0h1.125A1.875 1.875 0 0119.5 8.625v.75A1.875 1.875 0 0117.625 11.25H16.5m0-4.5H7.5m0 0H6.375A1.875 1.875 0 004.5 8.625v.75a1.875 1.875 0 001.875 1.875H7.5" /></svg>
                </x-slot:icon>
            </x-stat-card>
            <x-stat-card label="{{ __('dashboard.open_tickets') }}" :value="$stats['open']" color="blue"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></x-slot:icon></x-stat-card>
            <x-stat-card label="{{ __('dashboard.assigned_tickets') }}" :value="$stats['assigned']" color="indigo"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg></x-slot:icon></x-stat-card>
            <x-stat-card label="{{ __('dashboard.in_progress') }}" :value="$stats['in_progress']" color="amber"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" /></svg></x-slot:icon></x-stat-card>
            <x-stat-card label="{{ __('dashboard.resolved') }}" :value="$stats['resolved']" color="emerald"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></x-slot:icon></x-stat-card>
            <x-stat-card label="{{ __('dashboard.closed') }}" :value="$stats['closed']" color="slate"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg></x-slot:icon></x-stat-card>
            <x-stat-card label="{{ __('dashboard.total_users') }}" :value="$stats['total_users']" color="purple"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a8.964 8.964 0 003.741-2.372A6.474 6.474 0 0018 15.75V18.72zm0 0A9.023 9.023 0 0112 21a9.023 9.023 0 01-6-2.28m12 0V19.5A2.25 2.25 0 0115.75 21h-7.5A2.25 2.25 0 016 19.5v-.78m12 0a9.023 9.023 0 00-12 0M15 7.5a3 3 0 11-6 0 3 3 0 016 0z" /></svg></x-slot:icon></x-stat-card>
            <x-stat-card label="{{ __('dashboard.total_technicians') }}" :value="$stats['total_technicians']" color="amber"><x-slot:icon><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 3.206a1.125 1.125 0 011.159-.286 8.25 8.25 0 018.117 8.117 1.125 1.125 0 01-.286 1.159l-7.037 7.037a3.75 3.75 0 01-5.304 0l-2.965-2.965a3.75 3.75 0 010-5.304l7.037-7.037z" /></svg></x-slot:icon></x-stat-card>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-6 xl:col-span-1">
                <h2 class="text-base font-semibold text-gray-900">{{ __('dashboard.quick_actions') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.quick_actions_subtitle') }}</p>

                <div class="mt-5 space-y-3">
                    <a href="{{ route('admin.users.create') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition-colors hover:border-primary-300 hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ __('dashboard.create_user') }}</p>
                            <p class="text-xs text-gray-500">{{ __('dashboard.create_user_desc') }}</p>
                        </div>
                        <span class="text-primary-600">→</span>
                    </a>
                    <a href="{{ route('admin.technicians.create') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition-colors hover:border-primary-300 hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ __('dashboard.create_technician') }}</p>
                            <p class="text-xs text-gray-500">{{ __('dashboard.create_technician_desc') }}</p>
                        </div>
                        <span class="text-primary-600">→</span>
                    </a>
                    <a href="{{ route('admin.tickets.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition-colors hover:border-primary-300 hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ __('dashboard.view_tickets') }}</p>
                            <p class="text-xs text-gray-500">{{ __('dashboard.view_tickets_desc') }}</p>
                        </div>
                        <span class="text-primary-600">→</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition-colors hover:border-primary-300 hover:bg-gray-50">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ __('dashboard.reports') }}</p>
                            <p class="text-xs text-gray-500">{{ __('dashboard.reports_desc') }}</p>
                        </div>
                        <span class="text-primary-600">→</span>
                    </a>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white xl:col-span-3">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">{{ __('dashboard.recent_tickets') }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.recent_tickets_subtitle') }}</p>
                        </div>
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto">
                            <div class="relative min-w-0 sm:w-72">
                                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <input type="search" name="ticket_search" value="{{ request('ticket_search') }}" placeholder="{{ __('dashboard.search_recent_tickets') }}" class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            </div>
                            <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('common.search') }}</button>
                        </form>
                    </div>
                </div>

                @if($recentTickets->isEmpty())
                    <x-empty-state title="{{ __('tickets.no_tickets') }}" description="{{ __('tickets.no_recent') }}" />
                @else
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    @php
                                        $ticketSorts = [
                                            'ticket_number' => __('tickets.ticket_number'),
                                            'title' => __('tickets.title'),
                                            'priority' => __('common.priority'),
                                            'status' => __('common.status'),
                                            'created_at' => __('tickets.created_date'),
                                        ];
                                    @endphp
                                    @foreach($ticketSorts as $field => $label)
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['ticket_sort' => $field, 'ticket_direction' => request('ticket_sort') === $field && request('ticket_direction') === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex items-center gap-1 hover:text-gray-700">
                                                {{ $label }}
                                            </a>
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('tickets.requester') }}</th>
                                     <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('tickets.assigned_technician') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentTickets as $ticket)
                                    <tr class="transition-colors hover:bg-gray-50/50">
                                        <td class="px-6 py-4 text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ Str::limit($ticket->title, 36) }}</td>
                                        <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                        <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->assignee?->name ?? __('tickets.unassigned') }}</td>
                                        <td class="px-6 py-4"><a href="{{ route('admin.tickets.show', $ticket) }}" class="text-sm font-medium text-primary-600 transition-colors hover:text-primary-700">{{ __('common.view') }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-gray-100 md:hidden">
                        @foreach($recentTickets as $ticket)
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
                                    <span>{{ $ticket->assignee?->name ?? __('tickets.unassigned') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if($recentTickets->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4">
                            {{ $recentTickets->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div id="recent-activity" class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ __('dashboard.recent_activity') }}</h2>
                            <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.recent_activity_subtitle') }}</p>
                </div>
            </div>

            @if($recentActivities->isEmpty())
                <p class="mt-6 text-sm text-gray-500">{{ __('dashboard.no_activity') }}</p>
            @else
                <div class="relative mt-6">
                    <div class="absolute bottom-0 left-4 top-0 w-px bg-gray-200"></div>
                    <div class="space-y-6">
                        @foreach($recentActivities as $activity)
                            <div class="relative flex gap-4 pl-10">
                                <div class="absolute left-2.5 top-1 h-3 w-3 rounded-full bg-primary-500 ring-4 ring-white"></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity->description ?: ucfirst(str_replace('_', ' ', $activity->action)) }}</p>
                                        @if($activity->ticket)
                                            <a href="{{ route('admin.tickets.show', $activity->ticket) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">{{ $activity->ticket->ticket_number }}</a>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        @if($activity->user)
                                            <span>{{ $activity->user->name }}</span>
                                            <span class="text-gray-300">·</span>
                                        @endif
                                        <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->format('M d, Y h:i A') }}</time>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
