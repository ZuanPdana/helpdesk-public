@props(['status'])

@php
    $classes = match($status->value) {
        'open' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'closed' => 'bg-slate-50 text-slate-700 ring-slate-600/20',
        default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $classes }}">
    {{ $status->label() }}
</span>
