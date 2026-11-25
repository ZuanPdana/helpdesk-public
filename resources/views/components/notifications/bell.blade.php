@props([
    'notifications' => collect(),
    'unreadCount' => 0,
    'readRouteName' => null,
    'viewAllRoute' => null,
    'viewAllLabel' => 'View All',
])

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button
        @click="open = !open"
        type="button"
        class="relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
        aria-label="Open notifications"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.08 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-600 px-1 text-[10px] font-semibold text-white">
                {{ min($unreadCount, 99) }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        class="absolute right-0 mt-2 w-96 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg"
    >
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                <p class="text-xs text-gray-500">{{ $unreadCount }} unread</p>
            </div>
            @if($unreadCount > 0)
                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">{{ $unreadCount }}</span>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="px-4 py-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318a9 9 0 11-6.364 0M12 3v6m0 0-2.5-2.5M12 9l2.5-2.5" />
                    </svg>
                </div>
                <h4 class="mt-3 text-sm font-semibold text-gray-900">No notifications yet</h4>
                <p class="mt-1 text-sm text-gray-500">You are all caught up.</p>
            </div>
        @else
            <div class="max-h-96 divide-y divide-gray-100 overflow-y-auto">
                @foreach($notifications as $notification)
                    <x-notifications.item :notification="$notification" :read-route-name="$readRouteName" />
                @endforeach
            </div>
        @endif

        @if($viewAllRoute)
            <div class="border-t border-gray-100 px-4 py-3">
                <a href="{{ $viewAllRoute }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                    {{ $viewAllLabel }}
                </a>
            </div>
        @endif
    </div>
</div>
