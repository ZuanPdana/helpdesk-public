@props(['label', 'value', 'color' => 'primary', 'icon' => null])

@php
    $colorClasses = match($color) {
        'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
        'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'slate' => 'bg-slate-50 text-slate-600 border-slate-100',
        'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
        default => 'bg-primary-50 text-primary-600 border-primary-100',
    };
    $iconBg = match($color) {
        'blue' => 'bg-blue-100 text-blue-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'emerald' => 'bg-emerald-100 text-emerald-600',
        'slate' => 'bg-slate-100 text-slate-600',
        'indigo' => 'bg-indigo-100 text-indigo-600',
        'purple' => 'bg-purple-100 text-purple-600',
        default => 'bg-primary-100 text-primary-600',
    };
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-sm transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="w-11 h-11 rounded-lg {{ $iconBg }} flex items-center justify-center shrink-0">
                {{ $icon }}
            </div>
        @endif
    </div>
</div>
