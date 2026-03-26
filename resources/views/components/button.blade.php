@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center rounded-2xl font-semibold transition focus:outline-none focus:ring-2 focus:ring-brand-500/30 disabled:cursor-not-allowed disabled:opacity-60';

    $variantClasses = match ($variant) {
        'secondary' => 'border border-gray-200 bg-white text-gray-900 hover:bg-gray-50',
        default => 'bg-brand-600 text-white hover:bg-brand-700',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-5 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $classes = trim($baseClasses.' '.$variantClasses.' '.$sizeClasses);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
