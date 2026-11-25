@props(['role'])

@php
    $roleValue = $role instanceof \App\Enums\UserRole ? $role->value : (string) $role;
    $roleLabel = $role instanceof \App\Enums\UserRole ? $role->label() : ucfirst($roleValue);
    $classes = match ($roleValue) {
        'admin' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'technician' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        default => 'bg-blue-50 text-blue-700 ring-blue-600/20',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $classes }}">
    {{ $roleLabel }}
</span>
