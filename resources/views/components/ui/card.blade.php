@props([
    'tone' => 'white',
    'padding' => 'md',
    'rounded' => '2xl',
    'as' => 'div',
])

@php
    $tones = [
        'white' => 'border border-gray-100 bg-white shadow-sm',
        'plain' => 'bg-white',
        'muted' => 'border border-slate-200 bg-slate-50',
        'sage' => 'border border-sage-100 bg-sage-50',
        'blue' => 'border border-blue-100 bg-blue-50',
        'amber' => 'border border-amber-100 bg-amber-50',
    ];

    $paddings = [
        'none' => '',
        'xs' => 'p-3.5',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];

    $roundings = [
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        '3xl' => 'rounded-3xl',
    ];
@endphp

<{{ $as }} {{ $attributes->class([
    $tones[$tone] ?? $tones['white'],
    $paddings[$padding] ?? '',
    $roundings[$rounded] ?? '',
]) }}>
    {{ $slot }}
</{{ $as }}>