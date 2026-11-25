@props(['priority'])

@php
    $classes = match($priority->value) {
        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'medium' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'high' => 'bg-red-50 text-red-700 ring-red-600/20',
        'urgent' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $classes }}">
    {{ $priority->label() }}
</span>
