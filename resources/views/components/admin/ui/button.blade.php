@props([
    'variant' => 'secondary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-pine-900 text-white hover:bg-pine-950',
        'secondary' => 'border border-line-strong bg-white text-pine-900 hover:border-pine-900/30 hover:bg-sage-50',
        'ghost' => 'text-pine-900 hover:bg-pine-900/[0.06]',
        'danger' => 'text-red-700 hover:bg-red-50',
    ];

    $sizes = [
        'sm' => 'h-8 px-3 text-[13px]',
        'md' => 'h-9 px-3.5 text-[13.5px]',
        'lg' => 'h-11 px-5 text-sm',
    ];

    $classes = 'inline-flex items-center justify-center gap-1.5 rounded-control font-semibold whitespace-nowrap transition-colors disabled:pointer-events-none disabled:opacity-50 '
        .($variants[$variant] ?? $variants['secondary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
