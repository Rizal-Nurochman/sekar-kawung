@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-[13px] text-ink-muted">
            @if ($paginator->firstItem())
                {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
            @else
                0 dari {{ $paginator->total() }}
            @endif
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex size-8 items-center justify-center rounded-control text-ink-muted/40" aria-disabled="true">
                    <x-lucide-chevron-left class="size-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-control text-ink-soft transition-colors hover:bg-pine-900/[0.06] hover:text-pine-900">
                    <x-lucide-chevron-left class="size-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex size-8 items-center justify-center text-[13px] text-ink-muted" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex size-8 items-center justify-center rounded-control bg-pine-900 text-[13px] font-semibold tabular-nums text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex size-8 items-center justify-center rounded-control text-[13px] font-medium tabular-nums text-ink-soft transition-colors hover:bg-pine-900/[0.06] hover:text-pine-900">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-control text-ink-soft transition-colors hover:bg-pine-900/[0.06] hover:text-pine-900">
                    <x-lucide-chevron-right class="size-4" />
                </a>
            @else
                <span class="inline-flex size-8 items-center justify-center rounded-control text-ink-muted/40" aria-disabled="true">
                    <x-lucide-chevron-right class="size-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
