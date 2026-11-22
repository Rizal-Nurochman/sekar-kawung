@props([
    'title' => null,
    'description' => null,
    'flush' => false,
])

<section {{ $attributes->merge(['class' => 'rounded-panel border border-line bg-white']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-start justify-between gap-3 border-b border-line px-5 py-3.5">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-sm font-semibold text-pine-900">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-[13px] leading-5 text-ink-muted">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="{{ $flush ? '' : 'px-5 py-4' }}">
        {{ $slot }}
    </div>
</section>
