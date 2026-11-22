@props(['tone' => 'neutral'])

@php
    $tones = [
        'published' => 'bg-emerald-50 text-emerald-800 ring-emerald-700/20',
        'draft' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
        'warning' => 'bg-amber-50 text-amber-900 ring-amber-700/25',
        'neutral' => 'bg-sage-50 text-ink-soft ring-pine-900/10',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-badge px-2 py-0.5 text-xs font-semibold ring-1 ring-inset '.($tones[$tone] ?? $tones['neutral'])]) }}>
    <span class="size-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    {{ $slot }}
</span>
