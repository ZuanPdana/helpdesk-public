@props([
    'user',
    'size' => 'sm',
    'color' => 'primary',
])

@php
    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-16 w-16 text-xl',
        'xl' => 'h-20 w-20 text-2xl',
        'profile' => 'h-28 w-28 text-3xl',
    ];
    $colors = [
        'primary' => 'bg-primary-100 text-primary-700',
        'amber' => 'bg-amber-100 text-amber-700',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['sm'];
    $colorClasses = $colors[$color] ?? $colors['primary'];
@endphp

@if($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" {{ $attributes->merge(['class' => $sizeClasses.' shrink-0 rounded-full object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => $sizeClasses.' flex shrink-0 items-center justify-center rounded-full font-bold '.$colorClasses]) }}>
        {{ $user->initials() }}
    </div>
@endif