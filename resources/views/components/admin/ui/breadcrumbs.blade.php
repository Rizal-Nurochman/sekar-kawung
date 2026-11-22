@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-1.5']) }}>
    @foreach ($items as $item)
        @if (! $loop->first)
            <x-lucide-chevron-right class="size-3.5 text-ink-muted/50" />
        @endif

        @if (! empty($item['url']) && ! $loop->last)
            <a href="{{ $item['url'] }}" class="rounded transition-colors hover:text-pine-900 hover:underline">{{ $item['label'] }}</a>
        @else
            <span @if ($loop->last) aria-current="page" @endif @class(['font-medium text-ink-soft' => $loop->last])>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
