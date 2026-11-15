@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
    'weight' => 'medium',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 transition-[color,background-color,border-color,box-shadow,transform] duration-200';

    $variants = [
        'primary' => 'rounded-xl bg-sage-600 text-white shadow-md shadow-sage-600/20 hover:bg-sage-700 hover:shadow-lg',
        'primary-light' => 'rounded-xl bg-sage-500 text-white shadow-lg shadow-sage-500/30 hover:bg-sage-600',
        'secondary' => 'rounded-xl border border-slate-700 bg-slate-800/80 text-white backdrop-blur-md hover:bg-slate-800',
        'ghost' => 'rounded-lg p-2 text-slate-600 hover:text-slate-900',
        'icon' => 'size-11 rounded-full bg-white text-slate-700 shadow-sm ring-1 ring-slate-900/10 hover:bg-slate-50 hover:text-slate-900',
        'filter' => 'rounded-xl px-5 py-2.5 text-sm font-medium',
        'tab' => 'whitespace-nowrap border-b-2 pb-3 text-sm',
        'segmented' => 'min-h-11 rounded-lg px-3 text-sm',
    ];

    $sizes = [
        'sm' => 'px-6 py-2.5 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3.5 text-base',
        'xl' => 'px-8 py-4 text-base',
    ];

    $weights = [
        'medium' => 'font-medium',
        'semibold' => 'font-semibold',
        'bold' => 'font-bold',
    ];

    $isBare = in_array($variant, ['ghost', 'icon', 'filter', 'tab', 'segmented'], true);
@endphp

<{{ $href ? 'a' : 'button' }}
    @if ($href)
        href="{{ $href }}"
    @else
        type="{{ $type }}"
    @endif
    {{ $attributes->class([
        $base,
        $variants[$variant],
        ($sizes[$size] ?? $sizes['md']) => ! $isBare,
        ($weights[$weight] ?? $weights['medium']) => ! $isBare,
        'w-full' => $fullWidth,
    ]) }}
>
    {{ $slot }}
</{{ $href ? 'a' : 'button' }}>