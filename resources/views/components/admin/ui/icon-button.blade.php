@props([
    'label',
    'href' => null,
    'tone' => 'default',
    'type' => 'button',
])

@php
    $tones = [
        'default' => 'text-ink-muted hover:bg-pine-900/[0.06] hover:text-pine-900',
        'danger' => 'text-ink-muted hover:bg-red-50 hover:text-red-700',
    ];

    $classes = 'inline-flex size-8 items-center justify-center rounded-control transition-colors '
        .($tones[$tone] ?? $tones['default']);
@endphp

@if ($href)
    <a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
