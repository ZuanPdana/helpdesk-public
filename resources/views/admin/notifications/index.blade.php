@extends('layouts.admin')

@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">Notifications</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="mt-1 text-sm text-gray-500">View and manage your system notifications.</p>
            </div>

            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                    Mark All as Read
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid gap-3 lg:grid-cols-[repeat(3,minmax(0,1fr))_auto]">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search notifications..."
                            class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                        >
                    </div>
                </div>

                <select name="filter" class="rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                    <option value="all" @selected($filter === 'all')>All</option>
                    <option value="unread" @selected($filter === 'unread')>Unread</option>
                    <option value="read" @selected($filter === 'read')>Read</option>
                </select>

                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-slate-800">
                    Filter
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            @if($notifications->isEmpty())
                <x-empty-state title="No notifications yet" description="You're all caught up." />
            @else
                <div class="divide-y divide-gray-100">
                    @php
                        $currentGroup = null;
                    @endphp
                    @foreach($notifications as $notification)
                        @php
                            $groupLabel = $notification->created_at->isToday()
                                ? 'Today'
                                : ($notification->created_at->isYesterday()
                                    ? 'Yesterday'
                                    : $notification->created_at->format('F j, Y'));
                        @endphp

                        @if($currentGroup !== $groupLabel)
                            <div class="border-b border-gray-100 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                {{ $groupLabel }}
                            </div>
                            @php
                                $currentGroup = $groupLabel;
                            @endphp
                        @endif

                        <x-notifications.item :notification="$notification" read-route-name="admin.notifications.read" />
                    @endforeach
                </div>

                @if($notifications->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
