@props([
    'label',
    'value',
    'unit' => null,
    'meta' => null,
])

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <dt class="text-[13px] font-medium text-ink-muted">{{ $label }}</dt>
    <dd class="mt-1 flex flex-wrap items-baseline gap-x-1.5">
        <span class="text-2xl font-bold leading-8 tracking-tight tabular-nums text-pine-900">{{ $value }}</span>
        @if ($unit)
            <span class="text-[13px] font-medium text-ink-muted">{{ $unit }}</span>
        @endif
    </dd>
    @if ($meta)
        <dd class="mt-1 text-[13px] leading-5 text-ink-muted">{{ $meta }}</dd>
    @endif
</div>
