@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-3 px-6 py-12 text-center']) }}>
    @isset($icon)
        <span class="flex size-11 items-center justify-center rounded-full bg-sage-50 text-sage-600 [&_svg]:size-5">
            {{ $icon }}
        </span>
    @endisset

    <div>
        <p class="text-sm font-semibold text-pine-900">{{ $title }}</p>
        @if ($description)
            <p class="mx-auto mt-1 max-w-[48ch] text-[13px] leading-6 text-ink-muted">{{ $description }}</p>
        @endif
    </div>

    @isset($action)
        <div class="mt-1">{{ $action }}</div>
    @endisset
</div>
