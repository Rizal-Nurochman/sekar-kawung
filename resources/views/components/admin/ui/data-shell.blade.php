@props([
    'title',
    'count' => null,
    'countLabel' => 'data',
    'createLabel' => null,
    'createRoute' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="truncate text-[13px] text-ink-muted">
            @if ($count !== null)
                <span class="font-semibold tabular-nums text-pine-900">{{ $count }}</span> {{ $countLabel }}
            @else
                {{ $title }}
            @endif
        </p>

        @if ($createRoute)
            <x-admin.ui.button :href="$createRoute" variant="primary" size="sm">
                <x-lucide-plus />
                {{ $createLabel ?? 'Tambah' }}
            </x-admin.ui.button>
        @endif
    </div>

    <div class="overflow-hidden rounded-panel border border-line bg-white">
        {{ $slot }}
    </div>

    @isset($footer)
        <div>{{ $footer }}</div>
    @endisset
</div>
