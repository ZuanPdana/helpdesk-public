@props([
    'notification',
    'readRouteName' => null,
])

@php
    $data = $notification->data;
    $isUnread = is_null($notification->read_at);
    $title = $data['title'] ?? 'Notification';
    $message = $data['message'] ?? '';
    $event = $data['event'] ?? 'ticket_created';
    $ticketNumber = $data['ticket_number'] ?? '';
    $actionUrl = $data['action_url'] ?? '#';
    $iconClasses = match ($event) {
        'ticket_created' => 'bg-blue-50 text-blue-600',
        'ticket_assigned' => 'bg-indigo-50 text-indigo-600',
        'ticket_status_changed' => 'bg-amber-50 text-amber-600',
        'ticket_comment_added' => 'bg-emerald-50 text-emerald-600',
        'ticket_resolution_evidence_uploaded' => 'bg-purple-50 text-purple-600',
        default => 'bg-gray-100 text-gray-500',
    };
@endphp

@if($readRouteName)
    <form method="POST" action="{{ route($readRouteName, $notification->id) }}" class="contents">
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="flex w-full gap-3 px-4 py-3 text-left transition-colors {{ $isUnread ? 'bg-primary-50/40 hover:bg-primary-50' : 'hover:bg-gray-50' }}"
        >
            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $iconClasses }}">
                @switch($event)
                    @case('ticket_created')
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        @break
                    @case('ticket_assigned')
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25V9m7.5 0h2.25A2.25 2.25 0 0121 11.25v6A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25v-6A2.25 2.25 0 015.25 9H7.5" /></svg>
                        @break
                    @case('ticket_status_changed')
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0-4.5-4.5M19.5 12l-4.5 4.5" /></svg>
                        @break
                    @case('ticket_comment_added')
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m8.25 2.25a8.25 8.25 0 10-16.5 0c0 1.3.3 2.53.83 3.62L3.75 21l3.88-1.06c1.08.58 2.32.91 3.64.91a8.25 8.25 0 008.25-8.25z" /></svg>
                        @break
                    @case('ticket_resolution_evidence_uploaded')
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0 4.5-4.5M12 19.5l-4.5-4.5M6.75 7.5h10.5" /></svg>
                        @break
                    @default
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318a9 9 0 11-6.364 0M12 3v6" /></svg>
                @endswitch
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            @if($isUnread)
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-600"></span>
                            @endif
                            <p class="truncate text-sm font-semibold {{ $isUnread ? 'text-gray-900' : 'text-gray-800' }}">{{ $title }}</p>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                        @if($ticketNumber)
                            <p class="mt-1 text-xs font-medium text-gray-400">#{{ $ticketNumber }}</p>
                        @endif
                    </div>
                    <span class="shrink-0 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </button>
    </form>
@else
    <a
        href="{{ $actionUrl }}"
        class="flex gap-3 px-4 py-3 transition-colors {{ $isUnread ? 'bg-primary-50/40 hover:bg-primary-50' : 'hover:bg-gray-50' }}"
    >
        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $iconClasses }}">
            @switch($event)
                @case('ticket_created')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    @break
                @case('ticket_assigned')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25V9m7.5 0h2.25A2.25 2.25 0 0121 11.25v6A2.25 2.25 0 0118.75 19.5H5.25A2.25 2.25 0 013 17.25v-6A2.25 2.25 0 015.25 9H7.5" /></svg>
                    @break
                @case('ticket_status_changed')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0-4.5-4.5M19.5 12l-4.5 4.5" /></svg>
                    @break
                @case('ticket_comment_added')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m8.25 2.25a8.25 8.25 0 10-16.5 0c0 1.3.3 2.53.83 3.62L3.75 21l3.88-1.06c1.08.58 2.32.91 3.64.91a8.25 8.25 0 008.25-8.25z" /></svg>
                    @break
                @case('ticket_resolution_evidence_uploaded')
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0 4.5-4.5M12 19.5l-4.5-4.5M6.75 7.5h10.5" /></svg>
                    @break
                @default
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318a9 9 0 11-6.364 0M12 3v6" /></svg>
            @endswitch
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        @if($isUnread)
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-600"></span>
                        @endif
                        <p class="truncate text-sm font-semibold {{ $isUnread ? 'text-gray-900' : 'text-gray-800' }}">{{ $title }}</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                    @if($ticketNumber)
                        <p class="mt-1 text-xs font-medium text-gray-400">#{{ $ticketNumber }}</p>
                    @endif
                </div>
                <span class="shrink-0 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </a>
@endif
