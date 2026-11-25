@extends('layouts.user')

@section('title', __('dashboard.title'))
@section('page_title', __('dashboard.title'))

@section('content')
    {{-- Welcome header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('dashboard.hello_greeting', ['name' => auth()->user()->name]) }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.user_subtitle') }}</p>
        </div>
        <a href="{{ route('user.tickets.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('tickets.create_ticket') }}
        </a>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <x-stat-card label="{{ __('dashboard.total_tickets') }}" :value="$stats['total']" color="primary">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="{{ __('dashboard.open_tickets') }}" :value="$stats['open']" color="blue">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="{{ __('dashboard.assigned_tickets') }}" :value="$stats['assigned']" color="indigo">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="{{ __('dashboard.in_progress') }}" :value="$stats['in_progress']" color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="{{ __('dashboard.resolved') }}" :value="$stats['resolved']" color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="{{ __('dashboard.closed') }}" :value="$stats['closed']" color="slate">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- Recent tickets --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('dashboard.recent_tickets') }}</h2>
            <a href="{{ route('user.tickets.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                {{ __('common.view_all') }} →
            </a>
        </div>

        @if($recentTickets->isEmpty())
            <x-empty-state
                title="{{ __('tickets.no_tickets') }}"
                description="{{ __('tickets.empty_description') }}"
                :actionUrl="route('user.tickets.create')"
                actionLabel="{{ __('tickets.create_first') }}"
            />
        @else
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.ticket_id') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.subject') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.priority') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.status') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('tickets.technician') }}</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">{{ __('common.date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentTickets as $ticket)
                            <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('user.tickets.show', $ticket) }}'">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900 font-medium">{{ Str::limit($ticket->title, 40) }}</span>
                                </td>
                                <td class="px-6 py-4"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-6 py-4"><x-status-badge :status="$ticket->status" /></td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $ticket->assignee?->name ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($recentTickets as $ticket)
                    <a href="{{ route('user.tickets.show', $ticket) }}" class="block px-4 py-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <span class="text-xs font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                                <p class="text-sm font-medium text-gray-900 mt-0.5 truncate">{{ $ticket->title }}</p>
                            </div>
                            <x-status-badge :status="$ticket->status" />
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <x-priority-badge :priority="$ticket->priority" />
                            <span>{{ $ticket->assignee?->name ?? __('tickets.unassigned') }}</span>
                            <span>{{ $ticket->created_at->format('M d') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
